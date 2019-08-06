<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

declare(strict_types=1);

namespace Magento\AdobeStockAssetApi\Api\Data;

/**
 * Interface CreatorSearchResultsInterface
 * @api
 */
interface CreatorSearchResultsInterface extends \Magento\Framework\Api\SearchResultsInterface
{
    /**
     * Get category list.
     *
     * @return \Magento\AdobeStockAssetApi\Api\Data\CreatorInterface[]
     */
    public function getItems();

    /**
     * Set category list.
     *
     * @param \Magento\AdobeStockAssetApi\Api\Data\CreatorInterface[] $items
     * @return self
     */
    public function setItems(array $items);
}
