<?php

namespace Magento\AdobeStockClient\Model;

use AdobeStock\Api\Models\SearchParameters;
use Magento\AdobeStockClientApi\Api\SearchParameterProviderInterface;
use Magento\Framework\Api\SearchCriteriaInterface;

class SearchParametersProviderComposite implements SearchParameterProviderInterface
{
    /**
     * @var SearchParameterProviderInterface[]
     */
    private $providers;

    /**
     * SearchParametersProviderComposite constructor.
     * @param array $providers
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
