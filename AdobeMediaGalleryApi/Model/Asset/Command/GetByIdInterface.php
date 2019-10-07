<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

declare(strict_types=1);

namespace Magento\AdobeMediaGalleryApi\Model\Asset\Command;

/**
 * Get media asset by id.
 * @api
 */
interface GetByIdInterface
{
    /**
     * Get media asset by id.
     *
     * @param int $assetId
     *
     * @return \Magento\AdobeMediaGalleryApi\Api\Data\AssetInterface
     * @throws \Zend_Db_Statement_Exception
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function execute(int $assetId): \Magento\AdobeMediaGalleryApi\Api\Data\AssetInterface;
}
