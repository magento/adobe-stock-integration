<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

declare(strict_types=1);

use Magento\MediaGalleryApi\Api\Data\AssetInterface as MediaAsset;
use Magento\MediaGalleryApi\Api\Data\AssetInterfaceFactory as MediaAssetFactory;
use Magento\MediaGalleryApi\Model\Asset\Command\SaveInterface;
use Magento\AdobeStockAssetApi\Api\AssetRepositoryInterface;
use Magento\AdobeStockAssetApi\Api\Data\AssetInterface;
use Magento\AdobeStockAssetApi\Api\Data\AssetInterfaceFactory;
use Magento\AdobeStockAssetApi\Api\Data\CategoryInterface;
use Magento\AdobeStockAssetApi\Api\Data\CategoryInterfaceFactory;
use Magento\AdobeStockAssetApi\Api\Data\CreatorInterface;
use Magento\AdobeStockAssetApi\Api\Data\CreatorInterfaceFactory;
use Magento\AdobeStockAssetApi\Api\CategoryRepositoryInterface;
use Magento\AdobeStockAssetApi\Api\CreatorRepositoryInterface;
use Magento\TestFramework\Helper\Bootstrap;

$objectManager = Bootstrap::getObjectManager();
/** @var AssetRepositoryInterface $assetRepository */
$assetRepository = $objectManager->get(AssetRepositoryInterface::class);
/** @var MediaAssetFactory $mediaAssetFactory */
$mediaAssetFactory = $objectManager->get(MediaAssetFactory::class);
/** @var MediaAsset $mediaAsset */
$mediaAsset = $mediaAssetFactory->create(
    [
        'data' => [
            'path' => 'some/path.jpg'
        ]
    ]
);
/** @var SaveInterface $mediaSave */
$mediaSave = $objectManager->get(SaveInterface::class);
$mediaId = $mediaSave->execute($mediaAsset);

$categoryFactory = $objectManager->get(CategoryInterfaceFactory::class);
/** @var CategoryInterface $category */
$category = $categoryFactory->create(
    [
        'data' => [
            'id' => 42,
            'name' => 'Supercategory'
        ]
    ]
);
/** @var CategoryRepositoryInterface $categoryRepository */
$categoryRepository = $objectManager->get(CategoryRepositoryInterface::class);
$categoryId = $categoryRepository->save($category)->getId();

$creatorFactory = $objectManager->get(CreatorInterfaceFactory::class);
/** @var CreatorInterface $creator */
$creator = $creatorFactory->create(
    [
        'data' => [
            'id' => 56,
            'name' => 'Supercreator'
        ]
    ]
);
/** @var CreatorRepositoryInterface $creatorRepository */
$creatorRepository = $objectManager->get(CreatorRepositoryInterface::class);
$creatorId = $creatorRepository->save($creator)->getId();

/** @var AssetInterfaceFactory $assetFactory */
$assetFactory = $objectManager->get(AssetInterfaceFactory::class);
/** @var AssetInterface $asset */
$asset = $assetFactory->create(
    [
        'data' => [
            'id' => 1,
            'is_licensed' => 1,
            'category_id' => $categoryId,
            'creator_id' => $creatorId,
            'media_gallery_id' => $mediaId
        ]
    ]
);

$assetRepository->save($asset);
