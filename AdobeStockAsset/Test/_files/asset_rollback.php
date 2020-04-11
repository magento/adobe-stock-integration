<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

use Magento\AdobeStockAssetApi\Api\AssetRepositoryInterface;
use Magento\MediaGalleryApi\Model\Asset\Command\DeleteByPathInterface;
use Magento\TestFramework\Helper\Bootstrap;
use Magento\AdobeStockAssetApi\Api\CategoryRepositoryInterface;
use Magento\AdobeStockAssetApi\Api\CreatorRepositoryInterface;

$objectManager = Bootstrap::getObjectManager();

try {
    /** @var DeleteByPathInterface $deleteByPath */
    $deleteByPath = $objectManager->get(DeleteByPathInterface::class);
    $deleteByPath->execute('some/path.jpg');


    /** @var CreatorRepositoryInterface $creatorRepository */
    $creatorRepository = $objectManager->get(CreatorRepositoryInterface::class);
    $creatorRepository->deleteById(42);

    /** @var CategoryRepositoryInterface $categoryRepository */
    $categoryRepository = $objectManager->get(CategoryRepositoryInterface::class);
    $categoryRepository->deleteById(42);

    /** @var AssetRepositoryInterface $assetRepositoryRepository */
    $assetRepositoryRepository = $objectManager->get(AssetRepositoryInterface::class);
    $assetRepositoryRepository->deleteById(1);
} catch (\Exception $exception) {

}
