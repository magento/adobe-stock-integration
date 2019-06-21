<?php
/**
 *
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Magento\AdobeStockClientApi\Api;

use AdobeStock\Api\Models\SearchParameters;
use Magento\Framework\Api\SearchCriteriaInterface;

/**
 * Client for communication to Adobe Stock API
 */
interface SearchParameterProviderInterface
{
    /**
     * @param SearchCriteriaInterface $searchCriteria
     * @param SearchParameters $searchParams
     * @return SearchParameters
     */
    public function apply(
        SearchCriteriaInterface $searchCriteria,
        SearchParameters $searchParams
    ): SearchParameters;
}
