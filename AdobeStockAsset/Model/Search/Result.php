<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Magento\AdobeStockAsset\Model\Search;

class Result implements \Magento\AdobeStockAssetApi\Api\Data\SearchResultInterface
{
    /**
     * @var array
     */
    private $items;

    /**
     * @var int
     */
    private $count;

    /**
     * Result constructor.
     * @param \Magento\AdobeStockAssetApi\Api\Data\AssetInterface[] $items
     * @param int $count
     */
    public function __construct(array $items, int $count)
    {
        $this->items = $items;
        $this->count = $count;
    }

    /**
     * @return \Magento\AdobeStockAssetApi\Api\Data\AssetInterface[]
     */
    public function getItems() : array
    {
        return $this->items;
    }

    /**
     * @return int
     */
    public function getCount() : int
    {
        return $this->count;
    }
}
