#!/usr/bin/env bash

# Copyright © Magento, Inc. All rights reserved.
# See COPYING.txt for license details.

set -e
trap '>&2 echo Error: Command \`$BASH_COMMAND\` on line $LINENO failed with exit code $?' ERR

# prepare for test suite

if [[ ${TEST_SUITE} = "unit" ]]; then
      echo "Prepare unit tests for runining"
      composer require "mustache/mustache":"~2.5"
      composer require "php-coveralls/php-coveralls":"^1.0"
fi