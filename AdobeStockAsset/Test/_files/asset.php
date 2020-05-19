<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

declare(strict_types=1);

require __DIR__ . DIRECTORY_SEPARATOR . 'media_asset.php';
require __DIR__ . DIRECTORY_SEPARATOR . 'creator.php';
require __DIR__ . DIRECTORY_SEPARATOR . 'category.php';

use Magento\AdobeStockAssetApi\Api\AssetRepositoryInterface;
use Magento\AdobeStockAssetApi\Api\Data\AssetInterface;
use Magento\AdobeStockAssetApi\Api\Data\AssetInterfaceFactory;
use Magento\MediaGalleryApi\Api\GetAssetsByPathsInterface;
use Magento\TestFramework\Helper\Bootstrap;

$objectManager = Bootstrap::getObjectManager();

/** @var GetAssetsByPathsInterface $mediaGetByPath */
$mediaGetByPath = $objectManager->get(GetAssetsByPathsInterface::class);
$savedMediaAsset = $mediaGetByPath->execute(['some/path.jpg'])[0];

/** @var AssetInterfaceFactory $assetFactory */
$assetFactory = $objectManager->get(AssetInterfaceFactory::class);
/** @var AssetInterface $asset */
$asset = $assetFactory->create(
    [
        'data' => [
            'id' => 1,
            'is_licensed' => 1,
            'category_id' => 42,
            'creator_id' => 42,
            'media_gallery_id' => $savedMediaAsset->getId()
        ]
    ]
);

/** @var AssetRepositoryInterface $assetRepository */
$assetRepository = $objectManager->get(AssetRepositoryInterface::class);
$assetRepository->save($asset);
