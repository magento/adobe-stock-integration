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

/**
 * Is image separated from (and by) background color
 */
class Isolated implements SearchParameterProviderInterface
{
    private const ISOLATED_FILTER = 'isolated_filter';
    private const ISOLATED_ONLY = 'Isolated Only';

    /**
     * @inheritdoc
     */
    public function apply(SearchCriteriaInterface $searchCriteria, SearchParameters $searchParams): SearchParameters
    {
        foreach ($searchCriteria->getFilterGroups() as $filterGroup) {
            foreach ($filterGroup->getFilters() as $filter) {
                if ($filter->getField() === self::ISOLATED_FILTER && $filter->getValue() === self::ISOLATED_ONLY) {
                    $searchParams->setFilterIsolatedOn(true);
                    break;
                }
            }
        }
        return $searchParams;
    }
}
