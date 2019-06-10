<?php
/**
 *
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Magento\AdobeStockAssetApi\Api;

use AdobeStock\Api\Response\SearchFiles as SearchFilesResponse;
use Magento\AdobeStockAssetApi\Api\Data\SearchRequestInterface;
use Magento\AdobeStockAssetApi\Api\Data\SearchResultInterface;

/**
 * Interface
 */
interface ClientInterface
{
    /**
     * @param SearchRequestInterface $request
     * @return SearchResultInterface
     */
    public function search(SearchRequestInterface $request): SearchResultInterface;

    /**
     * @return SearchFilesResponse
     */
    public function testConnection();
}
