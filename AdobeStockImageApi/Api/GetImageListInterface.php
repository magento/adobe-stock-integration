<?php
/**
 *
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magento\AdobeStockImageApi\Api;

use Magento\Framework\Api\SearchCriteriaInterface;
use Magento\Framework\Api\SearchResultsInterface;

/**
 * Interface
 */
interface GetImageListInterface
{
    /**
     * @param SearchCriteriaInterface $searchCriteria
     * @return SearchResultsInterface
     */
    public function execute(SearchCriteriaInterface $searchCriteria): SearchResultsInterface;
}
