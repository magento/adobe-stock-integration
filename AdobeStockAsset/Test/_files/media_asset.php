<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

declare(strict_types=1);

use Magento\MediaGalleryApi\Api\Data\AssetInterface as MediaAsset;
use Magento\MediaGalleryApi\Api\Data\AssetInterfaceFactory as MediaAssetFactory;
use Magento\MediaGalleryApi\Api\SaveAssetsInterface;
use Magento\TestFramework\Helper\Bootstrap;

$objectManager = Bootstrap::getObjectManager();

/** @var MediaAssetFactory $mediaAssetFactory */
$mediaAssetFactory = $objectManager->get(MediaAssetFactory::class);
$mediaAsset = $mediaAssetFactory->create(
    [
        'path' => 'some/path.jpg',
        'title' => 'Web API test image',
        'source' => 'Adobe Stock',
        'contentType' => 'image/jpeg',
        'width' => 6529,
        'height' => 4355,
        'size' => 424242
    ]
);
/** @var SaveAssetsInterface $mediaSave */
$mediaSave = $objectManager->get(SaveAssetsInterface::class);
$mediaSave->execute([$mediaAsset]);
