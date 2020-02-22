#!/usr/bin/env bash

# Copyright © Magento, Inc. All rights reserved.
# See COPYING.txt for license details.

set -e
pushd magento2

if [ $TEST_SUITE == 'unit' ];
then
    vendor/bin/phpunit --configuration dev/tests/unit/phpunit.xml;
fi

if [ $TEST_SUITE == 'phpstan' ];
then
    composer require --dev phpstan/phpstan fooman/phpstan-magento2-magic-methods;
    vendor/bin/phpstan analyse -l 2 app/code/Magento/Adobe* -a dev/tests/api-functional/framework/autoload.php;
    vendor/bin/phpstan analyse -l 2 app/code/Magento/MediaGalleryUi -a dev/tests/api-functional/framework/autoload.php;
    vendor/bin/phpstan analyse -l 2 app/code/Magento/MediaGalleryUiApi -a dev/tests/api-functional/framework/autoload.php;
fi

if [ $TEST_SUITE == 'static' ]; then
    vendor/bin/phpcs --standard=dev/tests/static/framework/Magento/ app/code/Magento/Adobe* app/code/Magento/MediaGalleryUi app/code/Magento/MediaGalleryUiApi;
    ! find app/code/Magento/Adobe*/ -type f -name "*.php" -exec grep -L strict_types=1 {} + | grep Adobe;
    ! find app/code/Magento/MediaGalleryUi/ -type f -name "*.php" -exec grep -L strict_types=1 {} + | grep MediaGalleryUi;
    ! find app/code/Magento/MediaGalleryUiApi/ -type f -name "*.php" -exec grep -L strict_types=1 {} + | grep MediaGalleryUiApi;
fi

if [ $TEST_SUITE == 'functional' ]; then
    echo "Running MFTF suite ${MFTF_SUITE}";
    vendor/bin/mftf run:group $MFTF_SUITE --remove -vvv;
fi

popd
