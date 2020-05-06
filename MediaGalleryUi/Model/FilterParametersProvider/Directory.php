<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

declare(strict_types=1);

namespace Magento\MediaGalleryUi\Model\FilterParametersProvider;

use Magento\Framework\Api\SearchCriteriaInterface;
use Magento\Framework\DB\Select;
use Magento\MediaGalleryUi\Model\SelectModifierInterface;

/**
 * Apply path filter with regex to match path from beginning
 */
class Directory implements SelectModifierInterface
{
    private const DIRECTORY_FIELD_TYPE = 'path';

    /**
     * @inheritdoc
     */
    public function apply(Select $select, SearchCriteriaInterface $searchCriteria): void
    {
        foreach ($searchCriteria->getFilterGroups() as $filterGroup) {
            foreach ($filterGroup->getFilters() as $filter) {
                if ($filter->getField() === self::DIRECTORY_FIELD_TYPE) {
                    $value = str_replace('%', '', $filter->getValue());
                    $select->where('path REGEXP ? ', '^' . $value . '/[^\/]*$');
                }
            }
        }
    }
}
