<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

declare(strict_types=1);

use Magento\AdobeStockAssetApi\Api\AssetRepositoryInterface;
use Magento\AdobeStockAssetApi\Api\Data\AssetInterface;
use Magento\AdobeStockAssetApi\Api\Data\CategoryInterface;
use Magento\AdobeStockAssetApi\Api\Data\CreatorInterface;
use Magento\AdobeStockAssetApi\Api\Data\KeywordInterface;
use Magento\TestFramework\Helper\Bootstrap;

$objectManager = Bootstrap::getObjectManager();
/** @var AssetRepositoryInterface $assetRepository */
$assetRepository = $objectManager->get(AssetRepositoryInterface::class);

/** @var KeywordInterface[] $keywords */
$keywords =  require __DIR__ . '/../_files/objects/keywords.php';

/** @var CategoryInterface $category */
$category =  require __DIR__ . '/../_files/objects/category.php';

/** @var CreatorInterface $creator */
$creator =  require __DIR__ . '/../_files/objects/creator.php';

/** @var AssetInterface $asset */
$asset = require __DIR__ . '/../_files/objects/asset.php';

$asset->setCategory($category);
$asset->setCreator($creator);
$asset->setKeywords($keywords);

$assetRepository->save($asset);

return $asset;
