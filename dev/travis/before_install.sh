#!/usr/bin/env bash

# Copyright © Magento, Inc. All rights reserved.
# See COPYING.txt for license details.

set -e
trap '>&2 echo Error: Command \`$BASH_COMMAND\` on line $LINENO failed with exit code $?' ERR

# mock mail
sudo service postfix stop
echo # print a newline
smtp-sink -d "%d.%H.%M.%S" localhost:2500 1000 &
echo 'sendmail_path = "/usr/sbin/sendmail -t -i "' > ~/.phpenv/versions/$(phpenv version-name)/etc/conf.d/sendmail.ini

# disable xdebug and adjust memory limit
if [[ ${TEST_SUITE} != "unit" ]]; then
  echo > ~/.phpenv/versions/$(phpenv version-name)/etc/conf.d/xdebug.ini
fi
echo 'memory_limit = -1' >> ~/.phpenv/versions/$(phpenv version-name)/etc/conf.d/travis.ini
phpenv rehash;

# If env var is present, configure support for 3rd party builds which include private dependencies
test -n "$GITHUB_TOKEN" && composer config github-oauth.github.com "$GITHUB_TOKEN" || true

composer config --global http-basic.repo.magento.com "$MAGENTO_USERNAME" "$MAGENTO_PASSWORD"
# clone latest magento2 repo
git clone --depth 1 https://github.com/magento/magento2

if [[ ${TEST_SUITE} = "functional" ]]; then
    echo 'Creating magento2 database'
    mysql -uroot -e 'CREATE DATABASE magento2;'
    # Install apache
    sudo apt-get update
    sudo apt-get install apache2 libapache2-mod-fastcgi
    if [ ${TRAVIS_PHP_VERSION:0:1} == "7" ]; then
        sudo cp ${TRAVIS_BUILD_DIR}/magento2/dev/travis/config/www.conf ~/.phpenv/versions/$(phpenv version-name)/etc/php-fpm.d/
    fi

    # Enable php-fpm
    sudo cp ~/.phpenv/versions/$(phpenv version-name)/etc/php-fpm.conf.default ~/.phpenv/versions/$(phpenv version-name)/etc/php-fpm.conf
    sudo a2enmod rewrite actions fastcgi alias ssl
    echo "cgi.fix_pathinfo = 1" >> ~/.phpenv/versions/$(phpenv version-name)/etc/php.ini
    ~/.phpenv/versions/$(phpenv version-name)/sbin/php-fpm

    # Create key and self-signed SSL certificate
    sudo openssl req -new -newkey rsa:4096 -days 365 -nodes -x509 \
        -subj "/C=US/ST=Denial/L=Springfield/O=Dis/CN=magento2.travis" \
        -keyout "${MAGENTO_HOST_NAME}.key" -out "${MAGENTO_HOST_NAME}.cert"

    # Configure apache virtual hosts
    sudo cp -f ${TRAVIS_BUILD_DIR}/dev/travis/config/apache_virtual_host /etc/apache2/sites-available/000-default.conf
    sudo sed -e "s?%MAGENTO_ROOT%?${TRAVIS_BUILD_DIR}/magento2?g" --in-place /etc/apache2/sites-available/000-default.conf
    sudo sed -e "s?%MAGENTO_HOST_NAME%?${MAGENTO_HOST_NAME}?g" --in-place /etc/apache2/sites-available/000-default.conf
    sudo sed -e "s?%TRAVIS_BUILD_DIR%?${TRAVIS_BUILD_DIR}?g" --in-place /etc/apache2/sites-available/000-default.conf

    sudo usermod -a -G www-data travis
    sudo usermod -a -G travis www-data

    phpenv config-rm xdebug.ini
    sudo cat /etc/apache2/sites-enabled/000-default.conf
    sudo service apache2 restart

    # Install chromedriver to power chrome
    # get latest stable version of chromedriver, and ensure we have that
    CHROME_VERSION=$(curl https://chromedriver.storage.googleapis.com/LATEST_RELEASE)
    if [ ! -d "${HOME}/drivers" ]; then
        mkdir -p "${HOME}/drivers"
    fi
    if [ ! -f "${HOME}/drivers/chromedriver-${CHROME_VERSION}" ]; then
        pushd "${HOME}/drivers"
        rm -rf *
        wget "http://chromedriver.storage.googleapis.com/${CHROME_VERSION}/chromedriver_linux64.zip"
        unzip chromedriver_linux64.zip
        mv chromedriver "chromedriver-${CHROME_VERSION}"
        rm chromedriver_linux64.zip
        popd
    fi
    # TODO: if we are running headless chrome, we may not need xvfb
    # TODO: but we need to add the --headless flag to dev/tests/acceptance/tests/functional.suite.yml
    /sbin/start-stop-daemon --start --quiet --pidfile /tmp/custom_xvfb_99.pid --make-pidfile --background --exec /usr/bin/Xvfb -- :1 -screen 0 1280x1024x24
fi
