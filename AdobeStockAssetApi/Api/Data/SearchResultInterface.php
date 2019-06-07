<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Magento\AdobeStockAssetApi\Api\Data;

interface SearchResultInterface
{
    /**
     * Returns assets array
     *
     * @return \Magento\AdobeStockAssetApi\Api\Data\AssetInterface[]
     */
    public function getItems() : array;

    /**
     * Returns items count
     *
     * @return int
     */
    public function getCount() : int;
}
