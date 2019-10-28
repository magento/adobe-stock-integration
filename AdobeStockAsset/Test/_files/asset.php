<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

declare(strict_types=1);

use Magento\AdobeStockAssetApi\Api\AssetRepositoryInterface;
use Magento\AdobeStockAssetApi\Api\Data\AssetInterface;
use Magento\AdobeStockAssetApi\Api\Data\AssetInterfaceFactory;
use Magento\TestFramework\Helper\Bootstrap;

$objectManager = Bootstrap::getObjectManager();
/** @var AssetRepositoryInterface $assetRepository */
$assetRepository = $objectManager->get(AssetRepositoryInterface::class);
/** @var AssetInterfaceFactory $assetFactory */
$assetFactory = $objectManager->get(AssetInterfaceFactory::class);
/** @var AssetInterface $asset */
$asset = $assetFactory->create(
    [
        'data' => [
            'id' => 1,
            'is_licensed' => 1
        ]
    ]
);

$assetRepository->save($asset);
