<?php
/**
 *
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Magento\AdobeStockAssetApi\Api;

use Magento\AdobeStockAssetApi\Api\Data\SearchRequestInterface;
use Magento\AdobeStockAssetApi\Api\Data\SearchResultInterface;

/**
 * Client for communication to Adobe Stock API
 */
interface ClientInterface
{
    /**
     * Perform a call to Adobe Stock API to perform assets search based on the search request
     *
     * @param SearchRequestInterface $request
     * @return SearchResultInterface
     */
    public function search(SearchRequestInterface $request): SearchResultInterface;

    /**
     * Perform a basic request to Adobe Stock API to check network connection, API key, etc.
     *
     * @return bool
     */
    public function testConnection();
}
