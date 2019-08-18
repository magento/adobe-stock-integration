<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

declare(strict_types=1);

use Magento\AdobeStockAssetApi\Api\Data\CreatorInterface;
use Magento\AdobeStockAssetApi\Api\Data\CreatorInterfaceFactory;
use Magento\TestFramework\Helper\Bootstrap;

$objectManager = Bootstrap::getObjectManager();

/** @var CreatorInterfaceFactory $categoryFactory */
$creatorFactory = $objectManager->get(CreatorInterfaceFactory::class);

/** @var CreatorInterface $creator */
$creator = $creatorFactory->create();
$creator->setId('1');
$creator->setName('test creator');

return $creator;
