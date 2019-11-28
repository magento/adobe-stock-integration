<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

declare(strict_types=1);

namespace Magento\AdobeStockClient\Model;

use AdobeStock\Api\Models\SearchParameters;
use Magento\AdobeStockClient\Model\SearchParameterProviderInterface;
use Magento\Framework\Api\SearchCriteriaInterface;

/**
 * A class that keeping the list of providers responsible for filling SearchParameters based on search criteria
 */
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
     * Apply search criteria to SearchParameters
     *
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
