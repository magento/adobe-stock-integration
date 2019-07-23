<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

declare(strict_types=1);

namespace Magento\AdobeStockAssetApi\Api\Data;

/**
 * Interface MediaTypeSearchResultsInterface
 * @api
 */
interface MediaTypeSearchResultsInterface extends \Magento\Framework\Api\SearchResultsInterface
{
    /**
     * Get media type list.
     *
     * @return \Magento\AdobeStockAssetApi\Api\Data\MediaTypeInterface[]
     */
    public function getItems();

    /**
     * Set media type list.
     *
     * @param \Magento\AdobeStockAssetApi\Api\Data\MediaTypeInterface[] $items
     * @return $this
     */
    public function setItems(array $items);
}
