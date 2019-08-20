<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

declare(strict_types=1);

use Magento\AdobeStockAsset\Model\ResourceModel\Keyword;
use Magento\AdobeStockAssetApi\Api\Data\AssetInterface;
use Magento\AdobeStockAssetApi\Api\AssetRepositoryInterface;
use Magento\AdobeStockAssetApi\Api\CategoryRepositoryInterface;
use Magento\AdobeStockAssetApi\Api\CreatorRepositoryInterface;
use Magento\AdobeStockAssetApi\Api\Data\KeywordInterface;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\App\ResourceConnection;
use Magento\TestFramework\Helper\Bootstrap;

$objectManager = Bootstrap::getObjectManager();

/** @var AssetRepositoryInterface $assetRepository */
$assetRepository = $objectManager->get(AssetRepositoryInterface::class);
/** @var AssetInterface $asset */
try {
    $asset = $assetRepository->getById(1);
} catch (NoSuchEntityException $exception) {
    return;
}

/** @var CategoryRepositoryInterface $creatorRepository */
$categoryRepository = $objectManager->get(CategoryRepositoryInterface::class);
$categoryId = (int)$asset->getData('category_id');
if ($categoryId) {
    $categoryRepository->deleteById($categoryId);
}

/** @var CreatorRepositoryInterface $creatorRepository */
$creatorRepository = $objectManager->get(CreatorRepositoryInterface::class);
$creatorId = (int)$asset->getData('creator_id');
if ($creatorId) {
    $creatorRepository->deleteById($creatorId);
}

$keywords = $asset->getKeywords();
if (!empty($keywords)) {
    /** @var KeywordInterface $keyword */
    foreach ($keywords as $keyword) {
        $ids[] = $keyword->getId();
    }

    $connection = $objectManager->get(ResourceConnection::class)->getConnection();
    $connection->delete(
        $connection->getTableName(Keyword::TABLE_KEYWORD),
        $connection->quoteInto('id IN (?)', $ids)
    );
}

$assetRepository->deleteById($asset->getId());
