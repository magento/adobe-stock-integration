<?php

namespace Magento\AdobeStockClient\Model;

use AdobeStock\Api\Models\SearchParameters;
use Magento\AdobeStockClientApi\Api\SearchParameterProviderInterface;
use Magento\Framework\Api\SearchCriteriaInterface;

class SearchParametersProviderPool implements SearchParameterProviderInterface
{
    /**
     * @var SearchParameterProviderInterface[]
     */
    private $providers;

    /**
     * SearchParametersProviderPool constructor.
     * @param SearchParameterProviderInterface[] $providers
     */
    public function __construct(array $providers = [])
    {
        $this->providers = $providers;
    }

    public function apply(SearchCriteriaInterface $searchCriteria, SearchParameters $searchParams): SearchParameters
    {
        foreach ($this->providers as $provider) {
            $searchParams = $provider->apply($searchCriteria, $searchParams);
        }
        return $searchParams;
    }

    /**
     * @param SearchCriteriaInterface $searchCriteria
     * @param SearchParameters $searchParams
     * @return SearchParameters
     * @throws \AdobeStock\Api\Exception\StockApi
     */
    private function applyFilters(SearchCriteriaInterface $searchCriteria, SearchParameters $searchParams)
    {
        $mapping = [
            'words' => 'words',
            'content_type_filter' => 'content_type',
            'offensive_filter' => 'offensive',
            'isolated_filter' => 'isolated',
            'orientation_filter' => 'orientation',
            'colors_filter' => 'colors',
            'premium_price_filter' => 'premium',
        ];
        foreach ($searchCriteria->getFilterGroups() as $filterGroup) {
            foreach ($filterGroup->getFilters() as $filter) {
                if ($filter->getField() === 'words') {
                    $searchParams->setWords($filter->getValue());
                    continue;
                }

                $methodName = 'set' . ucfirst($mapping[$filter->getField()]);
                if (method_exists($searchParams, $methodName)) {
                    $searchParams->$methodName($filter->getValue());
                }

                $filterMethodName = 'setFilter' . ucfirst($mapping[$filter->getField()]);
                if (method_exists($searchParams, $filterMethodName)) {
                    $searchParams->$filterMethodName($filter->getValue());
                }
            }
        }
        return $searchParams;
    }
}
