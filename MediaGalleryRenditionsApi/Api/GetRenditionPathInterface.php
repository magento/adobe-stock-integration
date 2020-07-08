<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Magento\MediaGalleryRenditionsApi\Api;

use Magento\Framework\Exception\LocalizedException;
use Magento\MediaGalleryApi\Api\Data\AssetInterface;

interface GetRenditionPathInterface
{
    /**
     * Get Renditions image path
     *
     * @param AssetInterface $asset
     * @return string
     * @throws LocalizedException
     */
    public function execute(AssetInterface $asset): string;
}
