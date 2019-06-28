<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magento\AdobeStockClient\Model\SearchParametersProvider;

use AdobeStock\Api\Models\SearchParameters;
use Magento\AdobeStockClientApi\Api\SearchParameterProviderInterface;
use Magento\Framework\Api\SearchCriteriaInterface;

/**
 * Filter for images orientation: landscape, square, vertical, etc.
 */
class Orientation implements SearchParameterProviderInterface
{
    /**
     * @inheritdoc
     */
    public function apply(SearchCriteriaInterface $searchCriteria, SearchParameters $searchParams): SearchParameters
    {
        foreach ($searchCriteria->getFilterGroups() as $filterGroup) {
            foreach ($filterGroup->getFilters() as $filter) {
                if ($filter->getField() === 'orientation_filter') {
                    if ($filter->getValue() === 'PANORAMIC') {
                        $searchParams->setFilterPanoromicOn(true);
                        break;
                    }

                    $searchParams->setOrientation($filter->getValue());
                    break;
                }
            }
        }
        return $searchParams;
    }
}
