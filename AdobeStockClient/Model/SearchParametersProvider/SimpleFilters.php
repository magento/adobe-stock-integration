<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Magento\AdobeStockClient\Model;

use AdobeStock\Api\Models\SearchParameters;
use Magento\AdobeStockClientApi\Api\SearchParameterProviderInterface;
use Magento\Framework\Api\SearchCriteriaInterface;

/**
 * Apply filters that do not require additional business logic to search parameters
 */
class SimpleFilters implements SearchParameterProviderInterface
{
    /**
     * @var string[]
     */
    private $filters;

    /**
     * Filters constructor.
     * @param array $filters
     */
    public function __construct(array $filters = [])
    {
        $this->filters = $filters;
    }

    /**
     * Apply filter $name from $searchCriteria to $searchParams using $method method
     *
     * @param SearchCriteriaInterface $searchCriteria
     * @param SearchParameters $searchParams
     * @return SearchParameters
     */
    public function apply(SearchCriteriaInterface $searchCriteria, SearchParameters $searchParams): SearchParameters
    {
        foreach ($searchCriteria->getFilterGroups() as $filterGroup) {
            foreach ($filterGroup->getFilters() as $filter) {
                if (isset($this->filters[$filter->getField()])) {
                    $searchParams->{$this->filters[$filter->getField()]}($filter->getValue());
                }
            }
        }
        return $searchParams;
    }
}
