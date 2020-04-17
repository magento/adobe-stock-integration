<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

use Magento\TestFramework\Helper\Bootstrap;
use Magento\AdobeStockAssetApi\Api\CategoryRepositoryInterface;

$objectManager = Bootstrap::getObjectManager();

/** @var CategoryRepositoryInterface $categoryRepository */
$categoryRepository = $objectManager->get(CategoryRepositoryInterface::class);
$categoryRepository->deleteById(42);
