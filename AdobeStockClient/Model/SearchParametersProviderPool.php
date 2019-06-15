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

    /**
     * @param SearchCriteriaInterface $searchCriteria
     * @param SearchParameters $searchParams
     * @return SearchParameters
     */
    public function apply(SearchCriteriaInterface $searchCriteria, SearchParameters $searchParams): SearchParameters
    {
        foreach ($this->providers as $provider) {
            $searchParams = $provider->apply($searchCriteria, $searchParams);
        }
        return $searchParams;
    }
}
