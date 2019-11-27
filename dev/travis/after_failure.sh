#!/usr/bin/env bash

# Copyright © Magento, Inc. All rights reserved.
# See COPYING.txt for license details.

if [ "$TEST_SUITE" == 'functional' ]; then
    cat "${HOME}/selenium.log"
    cat "${TRAVIS_BUILD_DIR}/chromedriver.log"
    cat "${TRAVIS_BUILD_DIR}/magento2/var/log/debug.log"
    cat "${TRAVIS_BUILD_DIR}/magento2/var/log/system.log"
    pushd "${TRAVIS_BUILD_DIR}/magento2/dev/tests/acceptance/tests/_output"
    set +x
    for screenshot in *.png;
    do
        echo ""
        echo "Uploading ${screenshot}, image URL follows..."
        curl --location --request POST --form "image=@${screenshot}" "https://api.imgbb.com/1/upload?key=${IMGBB_API_KEY}" | jq '.data.url'
    done
    set -x
    popd
fi
