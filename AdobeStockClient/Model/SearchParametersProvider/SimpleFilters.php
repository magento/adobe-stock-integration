<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

declare(strict_types=1);

namespace Magento\AdobeStockClient\Model\SearchParametersProvider;

use AdobeStock\Api\Models\SearchParameters;
use Magento\AdobeStockClient\Model\SearchParameterProviderInterface;
use Magento\Framework\Api\SearchCriteriaInterface;
use Magento\Framework\Escaper;

/**
 * Apply filters that do not require additional business logic to search parameters
 */
class SimpleFilters implements SearchParameterProviderInterface
{
    /**
     * @var Escaper
     */
    private $escaper;

    /**
     * @var string[]
     */
    private $filters;

    /**
     * Filters constructor.
     *
     * @param Escaper $escaper
     * @param array $filters
     */
    public function __construct(
        Escaper $escaper,
        array $filters = []
    ) {
        $this->escaper = $escaper;
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
                    $value = $filter->getValue();
                    $searchParams->{$this->filters[$filter->getField()]}(
                        is_int($value) ?
                            $value :
                            $this->escaper->encodeUrlParam($filter->getValue())
                    );
                }
            }
        }
        return $searchParams;
    }
}
