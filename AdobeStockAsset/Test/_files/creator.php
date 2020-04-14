<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

declare(strict_types=1);

use Magento\AdobeStockAssetApi\Api\CreatorRepositoryInterface;
use Magento\AdobeStockAssetApi\Api\Data\CreatorInterface;
use Magento\AdobeStockAssetApi\Api\Data\CreatorInterfaceFactory;
use Magento\TestFramework\Helper\Bootstrap;

$objectManager = Bootstrap::getObjectManager();

/** @var CreatorInterfaceFactory $creatorFactory */
$creatorFactory = $objectManager->get(CreatorInterfaceFactory::class);
/** @var CreatorInterface $creator */
$creator = $creatorFactory->create(
    [
        'data' => [
            'id' => 42,
            'name' => 'Test creator'
        ]
    ]
);
/** @var CreatorRepositoryInterface $creatorRepository */
$creatorRepository = $objectManager->get(CreatorRepositoryInterface::class);
$creatorRepository->save($creator);
