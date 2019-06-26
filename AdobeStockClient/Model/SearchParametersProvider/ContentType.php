<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magento\AdobeStockClient\Model\SearchParametersProvider;

use AdobeStock\Api\Models\SearchParameters;
use Magento\AdobeStockClientApi\Api\SearchParameterProviderInterface;
use Magento\Framework\Api\SearchCriteriaInterface;

class ContentType implements SearchParameterProviderInterface
{
    /**
     * @inheritdoc
     */
    public function apply(SearchCriteriaInterface $searchCriteria, SearchParameters $searchParams): SearchParameters
    {
        // Set default filters state to add photos and illustrations to the results.
        $searchParams->setFilterContentTypePhotos(true);
        $searchParams->setFilterContentTypeIllustration(true);

        foreach ($searchCriteria->getFilterGroups() as $filterGroup) {
            foreach ($filterGroup->getFilters() as $filter) {
                if ($filter->getField() === 'content_type_filter') {
                    switch ($filter->getValue()) {
                        case 'photo':
                            $searchParams->setFilterContentTypeIllustration(false);
                            break;
                        case 'illustration':
                            $searchParams->setFilterContentTypePhotos(false);
                            break;
                    }
                    break;
                }
            }
        }
        return $searchParams;
    }
}
