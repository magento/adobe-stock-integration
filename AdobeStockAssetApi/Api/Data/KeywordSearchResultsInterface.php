<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

declare(strict_types=1);

namespace Magento\AdobeStockAssetApi\Api\Data;

/**
 * Interface KeywordSearchResultsInterface
 * @api
 */
interface KeywordSearchResultsInterface extends \Magento\Framework\Api\SearchResultsInterface
{
    /**
     * Get keyword list.
     *
     * @return \Magento\AdobeStockAssetApi\Api\Data\KeywordInterface[]
     */
    public function getItems();

    /**
     * Set keyword list.
     *
     * @param \Magento\AdobeStockAssetApi\Api\Data\KeywordInterface[] $items
     * @return self
     */
    public function setItems(array $items);
}
