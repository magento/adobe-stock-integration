<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

declare(strict_types=1);

namespace Magento\AdobeStockAssetApi\Api\Data;

/**
 * Interface PremiumLevelSearchResultsInterface
 * @api
 */
interface PremiumLevelSearchResultsInterface extends \Magento\Framework\Api\SearchResultsInterface
{
    /**
     * Get premium level list.
     *
     * @return \Magento\AdobeStockAssetApi\Api\Data\PremiumLevelInterface[]
     */
    public function getItems();

    /**
     * Set premium level list.
     *
     * @param \Magento\AdobeStockAssetApi\Api\Data\PremiumLevelInterface[] $items
     * @return $this
     */
    public function setItems(array $items);
}
