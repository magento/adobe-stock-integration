<?php
/**
 *
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magento\AdobeStockAssetApi\Api;

use Magento\AdobeStockAsset\Model\Search\Result;
use Magento\AdobeStockAssetApi\Api\Data\SearchRequestInterface;

/**
 * Interface
 */
interface ClientInterface
{
    /**
     * @param SearchRequestInterface $request
     * @return Result
     */
    public function search(SearchRequestInterface $request): Result;

    public function testConnection();
}
