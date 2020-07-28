<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

use Magento\AdobeStockAssetApi\Api\CreatorRepositoryInterface;
use Magento\TestFramework\Helper\Bootstrap;

$objectManager = Bootstrap::getObjectManager();

/** @var CreatorRepositoryInterface $creatorRepository */
$creatorRepository = $objectManager->get(CreatorRepositoryInterface::class);
$creatorRepository->deleteById(42);
