#!/usr/bin/env bash

# Copyright © Magento, Inc. All rights reserved.
# See COPYING.txt for license details.

set -e
trap '>&2 echo Error: Command \`$BASH_COMMAND\` on line $LINENO failed with exit code $?' ERR

# prepare for test suite
pushd magento2
if [[ ${TEST_SUITE} = "unit" ]]; then
    echo "Prepare unit tests for runining"
    pushd dev/tests/unit
    cp -f ${TRAVIS_BUILD_DIR}/dev/tests/unit/phpunit.xml phpunit.xml
    popd
    composer require "mustache/mustache":"~2.5"
    composer require "php-coveralls/php-coveralls":"^1.0"
fi

if [[ ${TEST_SUITE} = "functional" ]]; then
    echo "Installing Magento"
    php bin/magento setup:install \
        --language="en_US" \
        --timezone="UTC" \
        --currency="USD" \
        --base-url="${MAGENTO_PROTOCOL}://${MAGENTO_HOST_NAME}/" \
        --base-url-secure="${MAGENTO_PROTOCOL}://${MAGENTO_HOST_NAME}/" \
        --use-secure=1 \
        --use-secure-admin=1 \
        --admin-firstname="John" \
        --admin-lastname="Doe" \
        --backend-frontname="${MAGENTO_BACKEND}" \
        --admin-email="admin@example.com" \
        --admin-user="${MAGENTO_ADMIN_USERNAME}" \
        --use-rewrites=1 \
        --db-host=127.0.0.1 \
        --db-name=magento2 \
        --db-user=root \
        --cleanup-database \
        --admin-use-security-key=0 \
        --admin-password="${MAGENTO_ADMIN_PASSWORD}"
    echo "Enabling production mode"
    php bin/magento deploy:mode:set production
    # prepare magento instance as per getting started docs
    # https://devdocs.magento.com/mftf/docs/getting-started.html#prepare-magento
    echo "Preparing magento instance for MFTF usage"
    php bin/magento config:set cms/wysiwyg/enabled disabled
    php bin/magento config:set admin/security/admin_account_sharing 1
    php bin/magento config:set admin/security/use_form_key 0
    # check that site is up and running
    echo "Verifying magento instance is online"
    curl --insecure -L --show-error --fail -v "${MAGENTO_PROTOCOL}://${MAGENTO_HOST_NAME}"
    curl --insecure -L --show-error --fail -v "${MAGENTO_PROTOCOL}://${MAGENTO_HOST_NAME}/${MAGENTO_BACKEND}"

    echo "Prepare functional tests for running"

    composer require se/selenium-server-standalone:2.53.1
    export DISPLAY=:1.0

    pushd dev/tests/acceptance
    cp ./.htaccess.sample ./.htaccess
    cp -f ${TRAVIS_BUILD_DIR}/dev/tests/acceptance/.env .env
    sed -e "s?%MAGENTO_PROTOCOL%?${MAGENTO_PROTOCOL}?g" --in-place ./.env
    sed -e "s?%MAGENTO_HOST_NAME%?${MAGENTO_HOST_NAME}?g" --in-place ./.env
    sed -e "s?%MAGENTO_ADMIN_USERNAME%?${MAGENTO_ADMIN_USERNAME}?g" --in-place ./.env
    sed -e "s?%MAGENTO_ADMIN_PASSWORD%?${MAGENTO_ADMIN_PASSWORD}?g" --in-place ./.env
    cp -f ${TRAVIS_BUILD_DIR}/dev/tests/acceptance/.credentials .credentials
    sed -e "s?%ADOBE_STOCK_API_KEY%?${ADOBE_STOCK_API_KEY}?g" --in-place ./.credentials
    sed -e "s?%ADOBE_STOCK_PRIVATE_KEY%?${ADOBE_STOCK_PRIVATE_KEY}?g" --in-place ./.credentials
    sed -e "s?%ADOBE_STOCK_USER_EMAIL%?${ADOBE_STOCK_USER_EMAIL}?g" --in-place ./.credentials
    sed -e "s?%ADOBE_STOCK_USER_PASSWORD%?${ADOBE_STOCK_USER_PASSWORD}?g" --in-place ./.credentials
    popd

    # prepare mftf test files
    vendor/bin/mftf -V
    vendor/bin/codecept -V
    vendor/bin/mftf --verbose build:project
    vendor/bin/mftf --verbose generate:tests

    # run selenium
    # TODO: we should find different browsers here and possibly enable in a
    # travis matrix, for example:
    # headless chrome: https://docs.travis-ci.com/user/gui-and-headless-browsers/#using-the-chrome-addon-in-the-headless-mode
    # headless firefox: https://docs.travis-ci.com/user/gui-and-headless-browsers/#using-the-firefox-addon-in-headless-mode
    CHROME_VERSION=$(curl https://chromedriver.storage.googleapis.com/LATEST_RELEASE)
    sh ./vendor/se/selenium-server-standalone/bin/selenium-server-standalone -port 4444 -host 127.0.0.1 \
        -Dwebdriver.chrome.driver="${HOME}/drivers/chromedriver-${CHROME_VERSION}" \
        -Dwebdriver.chrome.args="--verbose" \
        -Dselenium.LOGGER.level="VERBOSE" \
        -Dwebdriver.chrome.logfile="${TRAVIS_BUILD_DIR}/chromedriver.log" \
        -trustAllSSLCertificate &> ~/selenium.log &
fi
popd
