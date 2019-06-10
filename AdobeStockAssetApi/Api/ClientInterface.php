<?php
/**
 *
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Magento\AdobeStockAssetApi\Api;

use Magento\AdobeStockAssetApi\Api\Data\SearchResultInterface;
use Magento\AdobeStockAssetApi\Api\Data\SearchRequestInterface;

/**
 * Interface
 */
interface ClientInterface
{
    /**
     * @param SearchRequestInterface $request
     * @return SearchResultInterface
     */
    public function search(SearchRequestInterface $request) : SearchResultInterface;
}
