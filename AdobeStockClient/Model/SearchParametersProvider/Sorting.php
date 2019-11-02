<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

declare(strict_types=1);

namespace Magento\AdobeStockClient\Model\SearchParametersProvider;

use AdobeStock\Api\Core\Constants;
use AdobeStock\Api\Models\SearchParameters;
use Magento\AdobeStockClient\Model\SearchParameterProviderInterface;
use Magento\Framework\Api\SearchCriteriaInterface;

/**
 * Apply selected sorting.
 */
class Sorting implements SearchParameterProviderInterface
{
    /**
     * @inheritdoc
     */
    public function apply(SearchCriteriaInterface $searchCriteria, SearchParameters $searchParams): SearchParameters
    {
        $resultsOrders = Constants::getSearchParamsOrders();
        foreach ($searchCriteria->getSortOrders() as $sortOrder) {
            if (false !== ($sortOrderField = array_search($sortOrder->getField(), $resultsOrders))) {
                $searchParams->setOrder($sortOrderField);
            }
        }

        return $searchParams;
    }
}
