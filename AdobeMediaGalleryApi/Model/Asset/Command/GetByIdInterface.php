<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

declare(strict_types=1);

namespace Magento\AdobeMediaGalleryApi\Model\Asset\Command;

/**
 * Get media asset by id
 * @api
 */
interface GetByIdInterface
{
    /**
     * Get media asset by id
     *
     * @param int $mediaAssetId
     *
     * @return \Magento\AdobeMediaGalleryApi\Api\Data\AssetInterface
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     * @throws \Magento\Framework\Exception\IntegrationException
     */
    public function execute(int $mediaAssetId): \Magento\AdobeMediaGalleryApi\Api\Data\AssetInterface;
}
