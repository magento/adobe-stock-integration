<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

declare(strict_types=1);

namespace Magento\MediaGalleryUi\Model;

use Magento\Framework\DB\Select;
use Magento\Framework\Api\SearchCriteriaInterface;

/**
 * A class that keeping the list of asset list filters
 */
class SelectModifierComposite implements SelectModifierInterface
{
    /**
     * @var SelectModifierInterface[]
     */
    private $selectModifiers;

    /**
     * @param SelectModifierInterface[] $selectModifiers
     */
    public function __construct(array $selectModifiers = [])
    {
        $this->selectModifiers = $selectModifiers;
    }

    /**
     * Apply search criteria to select
     *
     * @param Select $select
     * @param SearchCriteriaInterface $searchCriteria
     * @return void
     */
    public function apply(Select $select, SearchCriteriaInterface $searchCriteria): void
    {
        foreach ($this->selectModifiers as $selectModifier) {
            $selectModifier->apply($select, $searchCriteria);
        }
    }
}
