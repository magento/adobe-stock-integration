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
 * Filter for images orientation: landscape, square, vertical, etc.
 */
class Orientation implements SearchParameterProviderInterface
{
    private const ORIENTATION_FILTER = 'orientation_filter';
    private const PANORAMIC = 'PANORAMIC';

    /**
     * @inheritdoc
     */
    public function apply(SearchCriteriaInterface $searchCriteria, SearchParameters $searchParams): SearchParameters
    {
        foreach ($searchCriteria->getFilterGroups() as $filterGroup) {
            foreach ($filterGroup->getFilters() as $filter) {
                if ($filter->getField() === self::ORIENTATION_FILTER) {
                    if ($filter->getValue() === self::PANORAMIC) {
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
