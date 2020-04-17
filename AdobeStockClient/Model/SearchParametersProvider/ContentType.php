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
    private const PHOTO = 'photo';
    private const ILLUSRATION = 'illustration';
    private const CONTENT_TYPE_FILTER = 'content_type_filter';

    /**
     * @inheritdoc
     */
    public function apply(SearchCriteriaInterface $searchCriteria, SearchParameters $searchParams): SearchParameters
    {
        foreach ($searchCriteria->getFilterGroups() as $filterGroup) {
            foreach ($filterGroup->getFilters() as $filter) {
                if ($filter->getField() === self::CONTENT_TYPE_FILTER) {
                    $value = $filter->getValue();
                    if ($value == self::PHOTO) {
                        $searchParams->setFilterContentTypePhotos(true);
                    } elseif ($value == self::ILLUSRATION) {
                        $searchParams->setFilterContentTypeIllustration(true);
                    }
                }
            }
        }
        return $searchParams;
    }
}
