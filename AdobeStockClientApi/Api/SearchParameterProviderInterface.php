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
 * Class able to apply search criteria to SearchParameters completely or partially
 */
interface SearchParameterProviderInterface
{
    /**
     * Apply search criteria to SearchParameters
     *
     * @param SearchCriteriaInterface $searchCriteria
     * @param SearchParameters $searchParams
     * @return SearchParameters
     */
    public function apply(
        SearchCriteriaInterface $searchCriteria,
        SearchParameters $searchParams
    ): SearchParameters;
}
