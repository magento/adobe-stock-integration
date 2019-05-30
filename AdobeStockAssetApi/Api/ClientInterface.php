<?php
/**
 *
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magento\AdobeStockAssetApi\Api;

/**
 * Interface
 */
interface ClientInterface
{
    /**
     * @param Data\SearchRequestInterface $request
     * @return mixed
     */
    public function search(\Magento\AdobeStockAssetApi\Api\Data\SearchRequestInterface $request) : array;
}
