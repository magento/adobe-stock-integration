<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magento\AdobeStockAssetApi\Api\Data;

/**
 * Interface AssetRepositoryInterface
 * @api
 */
interface AssetSearchResultsInterface extends \Magento\Framework\Api\SearchResultsInterface
{
    /**
     * Get assets list.
     *
     * @return \Magento\AdobeStockAssetApi\Api\Data\AssetInterface[]
     */
    public function getItems();

    /**
     * Set assets list.
     *
     * @param \Magento\AdobeStockAssetApi\Api\Data\AssetInterface[] $items
     * @return $this
     */
    public function setItems(array $items);
}
