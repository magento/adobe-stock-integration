<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

declare(strict_types=1);

namespace Magento\AdobeStockImageApi\Api;

use Magento\AdobeStockAssetApi\Api\Data\AssetSearchResultsInterface;
use Magento\Framework\Api\SearchCriteriaInterface;

/**
 * Interface
 *
 * @api
 */
interface GetImageListInterface
{
    /**
     * Search for images based on search criteria
     *
     * @param \Magento\Framework\Api\SearchCriteriaInterface $searchCriteria
     * @return \Magento\AdobeStockAssetApi\Api\Data\AssetSearchResultsInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function execute(SearchCriteriaInterface $searchCriteria): AssetSearchResultsInterface;
}
