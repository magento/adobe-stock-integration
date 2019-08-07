<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

declare(strict_types=1);

namespace Magento\AdobeStockClient\Model\SearchParametersProvider;

use AdobeStock\Api\Models\SearchParameters;
use Magento\AdobeStockClientApi\Api\SearchParameterProviderInterface;
use Magento\Framework\Api\SearchCriteriaInterface;

/**
 * Adobe media id filter.
 */
class MediaId implements SearchParameterProviderInterface
{
    /**
     * @inheritdoc
     * @throws \AdobeStock\Api\Exception\StockApi
     */
    public function apply(SearchCriteriaInterface $searchCriteria, SearchParameters $searchParams): SearchParameters
    {
        foreach ($searchCriteria->getFilterGroups() as $filterGroup) {
            foreach ($filterGroup->getFilters() as $filter) {
                if ($filter->getField() === 'media_id') {
                    // Todo parameter expected to be int, string given
                    $searchParams->setMediaId((int)$filter->getValue());
                }
            }
        }

        return $searchParams;
    }
}
