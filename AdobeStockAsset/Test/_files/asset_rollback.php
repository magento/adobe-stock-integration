<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

require __DIR__ . DIRECTORY_SEPARATOR . 'media_asset_rollback.php';
require __DIR__ . DIRECTORY_SEPARATOR . 'creator_rollback.php';
require __DIR__ . DIRECTORY_SEPARATOR . 'category_rollback.php';

use Magento\AdobeStockAssetApi\Api\AssetRepositoryInterface;
use Magento\TestFramework\Helper\Bootstrap;

$objectManager = Bootstrap::getObjectManager();
/** @var AssetRepositoryInterface $assetRepositoryRepository */
$assetRepositoryRepository = $objectManager->get(AssetRepositoryInterface::class);
$assetRepositoryRepository->deleteById(1);
