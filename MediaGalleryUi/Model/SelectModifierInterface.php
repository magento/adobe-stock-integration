<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magento\MediaGalleryUi\Model;

use Magento\Framework\DB\Select;
use Magento\Framework\Api\SearchCriteriaInterface;

/**
 * Class to apply asset filter
 */
interface SelectModifierInterface
{
    /**
     * Apply search criteria to Select
     *
     * @param Select $select
     * @param SearchCriteriaInterface $searchCriteria
     * @return void
     */
    public function apply(Select $select, SearchCriteriaInterface $searchCriteria): void;
}
