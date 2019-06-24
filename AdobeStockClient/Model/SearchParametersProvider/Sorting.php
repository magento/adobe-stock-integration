<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Magento\AdobeStockClient\Model\SearchParametersProvider;

use AdobeStock\Api\Core\Constants;
use AdobeStock\Api\Models\SearchParameters;
use Magento\AdobeStockClientApi\Api\SearchParameterProviderInterface;
use Magento\Framework\Api\SearchCriteriaInterface;

class Sorting implements SearchParameterProviderInterface
{
    /**
     * Apply sorting
     *
     * @param SearchCriteriaInterface $searchCriteria
     * @param SearchParameters $searchParams
     * @return SearchParameters
     */
    public function apply(SearchCriteriaInterface $searchCriteria, SearchParameters $searchParams): SearchParameters
    {
        $resultsOrders = Constants::getSearchParamsOrders();
        foreach ($searchCriteria->getSortOrders() as $sortOrder) {
            if (false !== ($sortOrderField = array_search($sortOrder->getField(), $resultsOrders))) {
                $searchParams->setOrder($sortOrderField);
            }
        }

        return $searchParams;
    }
}
