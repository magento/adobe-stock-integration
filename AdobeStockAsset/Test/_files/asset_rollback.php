<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

use Magento\AdobeStockAssetApi\Api\AssetRepositoryInterface;
use Magento\AdobeStockAssetApi\Api\Data\AssetInterface;
use Magento\MediaGalleryApi\Api\Data\AssetInterface as MediaGalleryAsset;
use Magento\MediaGalleryApi\Model\Asset\Command\DeleteByPathInterface;
use Magento\MediaGalleryApi\Model\Asset\Command\GetByIdInterface;
use Magento\TestFramework\Helper\Bootstrap;
use Magento\AdobeStockAssetApi\Api\CategoryRepositoryInterface;
use Magento\AdobeStockAssetApi\Api\CreatorRepositoryInterface;

$objectManager = Bootstrap::getObjectManager();

try {
    /** @var AssetRepositoryInterface $assetRepositoryRepository */
    $assetRepositoryRepository = $objectManager->get(AssetRepositoryInterface::class);
    /** @var AssetInterface $asset */
    $asset = $assetRepositoryRepository->getById(1);

    /** @var GetByIdInterface $getAssetById */
    $getAssetById = $objectManager->get(GetByIdInterface::class);
    /** @var MediaGalleryAsset $mediaGalleryAsset */
    $mediaGalleryAsset = $getAssetById->execute($asset->getMediaGalleryId());
    /** @var DeleteByPathInterface $deleteByPath */
    $deleteByPath = $objectManager->get(DeleteByPathInterface::class);
    $deleteByPath->execute($mediaGalleryAsset->getPath());

    $assetRepositoryRepository->deleteById(1);

    /** @var CreatorRepositoryInterface $creatorRepository */
    $creatorRepository = $objectManager->get(CreatorRepositoryInterface::class);
    $creatorRepository->deleteById(42);

    /** @var CategoryRepositoryInterface $categoryRepository */
    $categoryRepository = $objectManager->get(CategoryRepositoryInterface::class);
    $categoryRepository->deleteById(42);
} catch (\Exception $exception) {

}
