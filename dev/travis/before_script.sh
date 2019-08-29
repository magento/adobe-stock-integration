#!/usr/bin/env bash

# Copyright © Magento, Inc. All rights reserved.
# See COPYING.txt for license details.

set -e
trap '>&2 echo Error: Command \`$BASH_COMMAND\` on line $LINENO failed with exit code $?' ERR

# prepare for test suite

if [[ $TEST_SUITE = "functional" ]] || [[ $TEST_SUITE = "api" ]] || [[ $TEST_SUITE = "integration" ]]; then
        echo "Installing Magento"
        php bin/magento setup:install -q \
            --language="en_US" \
            --timezone="UTC" \
            --currency="USD" \
            --base-url="http://${MAGENTO_HOST_NAME}/" \
            --admin-firstname="John" \
            --admin-lastname="Doe" \
            --backend-frontname="backend" \
            --admin-email="admin@example.com" \
            --admin-user="admin" \
            --use-rewrites=1 \
            --admin-use-security-key=0 \
            --admin-password="123123q"
fi

if [[ $TEST_SUITE = "functional" ]]; then
        echo "Enabling production mode"
        php bin/magento deploy:mode:set production

        echo "Prepare functional tests for running"

        composer require se/selenium-server-standalone:2.53.1
        export DISPLAY=:1.0
        sh ./vendor/se/selenium-server-standalone/bin/selenium-server-standalone -port 4444 -host 127.0.0.1 \
            -Dwebdriver.firefox.bin=$(which firefox) -trustAllSSLCertificate &> ~/selenium.log &

        cd dev/tests/acceptance

        cp ./.htaccess.sample ./.htaccess
        sed -e "s?%ADOBE_STOCK_API_KEY%?${ADOBE_STOCK_API_KEY}?g" --in-place ./.env
        sed -e "s?%ADOBE_STOCK_PRIVATE_KEY%?${ADOBE_STOCK_PRIVATE_KEY}?g" --in-place ./.env

        cd ../../..

        mftf build:project
        mftf generate:tests
fi

if [[ $TEST_SUITE = "api" ]]; then
        echo "Prepare api-functional tests for running"
        cd dev/tests/api-functional

        sed -e "s?magento.url?${MAGENTO_HOST_NAME}?g" --in-place ./phpunit.xml

        cd ../../..

        echo "Enabling production mode"
        php bin/magento deploy:mode:set production

        php bin/magento app:config:dump
        php bin/magento config:sensitive:set adobe_stock/integration/api_key ${ADOBE_STOCK_API_KEY}
        php bin/magento config:sensitive:set adobe_stock/integration/private_key ${ADOBE_STOCK_PRIVATE_KEY}
        php bin/magento cache:flush
fi