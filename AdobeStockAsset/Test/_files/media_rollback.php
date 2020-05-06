<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

use Magento\MediaGalleryApi\Api\DeleteAssetsByPathsInterface;
use Magento\TestFramework\Helper\Bootstrap;

$objectManager = Bootstrap::getObjectManager();

/** @var DeleteAssetsByPathsInterface $deleteByPaths */
$deleteByPaths = $objectManager->get(DeleteAssetsByPathsInterface::class);
$deleteByPaths->execute(['some/path.jpg']);
