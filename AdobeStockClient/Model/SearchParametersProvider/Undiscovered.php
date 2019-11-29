<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

declare(strict_types=1);

namespace Magento\AdobeStockClient\Model\SearchParametersProvider;

use AdobeStock\Api\Core\Constants;
use AdobeStock\Api\Models\SearchParameters;
use Magento\AdobeStockClient\Model\SearchParameterProviderInterface;
use Magento\Framework\Api\SearchCriteriaInterface;

/**
 * Use this filter to view content that has never been downloaded
 */
class Undiscovered implements SearchParameterProviderInterface
{
    /**
     * @inheritdoc
     */
    public function apply(SearchCriteriaInterface $searchCriteria, SearchParameters $searchParams): SearchParameters
    {
        $resultsOrders = Constants::getSearchParamsOrders();
        foreach ($searchCriteria->getFilterGroups() as $filterGroup) {
            foreach ($filterGroup->getFilters() as $filter) {
                if ($filter->getField() === 'undiscovered_filter') {
                    if (false !== ($searchField = array_search('undiscovered', $resultsOrders))) {
                        $searchParams->setOrder($searchField);
                        break;
                    }
                }
            }
        }
        return $searchParams;
    }
}
