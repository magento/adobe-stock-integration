<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

declare(strict_types=1);

namespace Magento\AdobeMediaGalleryApi\Model\Asset\Command;

/**
 * Interface GetByPathInterface
 */
interface GetByPathInterface
{
    /**
     * Get media asset list
     *
     * @param string $mediaFilePath
     *
     * @return \Magento\AdobeMediaGalleryApi\Api\Data\AssetInterface
     */
    public function execute(string $mediaFilePath): \Magento\AdobeMediaGalleryApi\Api\Data\AssetInterface;
}
