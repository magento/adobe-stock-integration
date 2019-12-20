#!/usr/bin/env bash

# Copyright © Magento, Inc. All rights reserved.
# See COPYING.txt for license details.

find "${TRAVIS_BUILD_DIR}/magento2/var/report" -type f -exec cat {} \;
cat "${TRAVIS_BUILD_DIR}/magento2/var/log/exception.log"
cat "${TRAVIS_BUILD_DIR}/magento2/var/log/system.log"
