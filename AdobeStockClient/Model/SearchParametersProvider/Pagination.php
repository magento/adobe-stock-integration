<?php

namespace Magento\AdobeStockClient\Model\SearchParametersProvider;

use AdobeStock\Api\Models\SearchParameters;
use Magento\AdobeStockClientApi\Api\SearchParameterProviderInterface;
use Magento\Framework\Api\SearchCriteriaInterface;

/**
 * Handles pagination of search results
 */
class Pagination implements SearchParameterProviderInterface
{
    /**
     * @inheritdoc
     */
    public function apply(SearchCriteriaInterface $searchCriteria, SearchParameters $searchParams): SearchParameters
    {
        $searchParams->setLimit($searchCriteria->getPageSize() ?? 32);
        $searchParams->setOffset(($searchCriteria->getCurrentPage() - 1) * $searchCriteria->getPageSize());
        return $searchParams;
    }
}
