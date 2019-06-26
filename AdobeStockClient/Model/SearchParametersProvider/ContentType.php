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
        foreach ($searchCriteria->getFilterGroups() as $filterGroup) {
            foreach ($filterGroup->getFilters() as $filter) {
                if ($filter->getField() === 'content_type_filter_photo') {
                    $searchParams->setFilterContentTypePhotos((bool)$filter->getValue());
                    break;
                }
                if ($filter->getField() === 'content_type_filter_illustrations') {
                    $searchParams->setFilterContentTypeIllustration((bool)$filter->getValue());
                    break;
                }
            }
        }
        return $searchParams;
    }
}
