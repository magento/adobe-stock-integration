<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

declare(strict_types=1);

namespace Magento\AdobeStockAssetApi\Api\Data;

/**
 * Interface AssetSearchResultsInterface
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
