<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

declare(strict_types=1);

namespace Magento\AdobeStockAssetApi\Api;

/**
 * Get asset by Adobe ID
 *
 * @api
 */
interface GetAssetByIdInterface
{
    /**
     * Returns asset by Adobe ID
     *
     * @param int $adobeId
     * @return \Magento\Framework\Api\Search\DocumentInterface
     * @throws \Magento\Framework\Exception\NotFoundException
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function execute(int $adobeId): \Magento\Framework\Api\Search\DocumentInterface;
}
