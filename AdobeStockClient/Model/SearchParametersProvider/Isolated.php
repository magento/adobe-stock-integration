<?php

namespace Magento\AdobeStockClient\Model\SearchParametersProvider;

use AdobeStock\Api\Models\SearchParameters;
use Magento\AdobeStockClientApi\Api\SearchParameterProviderInterface;
use Magento\Framework\Api\SearchCriteriaInterface;

/**
 * Is image separated from (and by) background color
 */
class Isolated implements SearchParameterProviderInterface
{
    /**
     * @inheritdoc
     */
    public function apply(SearchCriteriaInterface $searchCriteria, SearchParameters $searchParams): SearchParameters
    {
        foreach ($searchCriteria->getFilterGroups() as $filterGroup) {
            foreach ($filterGroup->getFilters() as $filter) {
                if ($filter->getField() === 'isolated_filter') {
                    $searchParams->setFilterIsolatedOn((bool)$filter->getValue());
                    break;
                }
            }
        }
        return $searchParams;
    }
}
