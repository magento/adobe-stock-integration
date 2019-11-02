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
 * Photo or illustration image type filter
 */
class ContentType implements SearchParameterProviderInterface
{
    /**
     * @inheritdoc
     */
    public function apply(SearchCriteriaInterface $searchCriteria, SearchParameters $searchParams): SearchParameters
    {
        foreach ($searchCriteria->getFilterGroups() as $filterGroup) {
            foreach ($filterGroup->getFilters() as $filter) {
                if ($filter->getField() === 'content_type_filter') {
                    switch ($filter->getValue()) {
                        case 'photo':
                            $searchParams->setFilterContentTypePhotos(true);
                            break;
                        case 'illustration':
                            $searchParams->setFilterContentTypeIllustration(true);
                            break;
                    }
                }
            }
        }
        return $searchParams;
    }
}
