<?php

namespace Magento\AdobeStockClient\Model\SearchParametersProvider;

use AdobeStock\Api\Models\SearchParameters;
use Magento\AdobeStockClientApi\Api\SearchParameterProviderInterface;
use Magento\Framework\Api\SearchCriteriaInterface;

class Offensive implements SearchParameterProviderInterface
{
    /**
     * @inheritDoc
     */
    public function apply(SearchCriteriaInterface $searchCriteria, SearchParameters $searchParams): SearchParameters
    {
        foreach ($searchCriteria->getFilterGroups() as $filterGroup) {
            foreach ($filterGroup->getFilters() as $filter) {
                if ($filter->getField() === 'offensive_filter') {
                    $searchParams->setFilterOffensive2($filter->getValue());
                    return $searchParams;
                }
            }
        }
        return $searchParams;
    }
}
