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
class FilterParametersProviderComposite implements SelectModifierInterface
{
    /**
     * @var SelectModifierInterface[]
     */
    private $providers;

    /**
     * @param SelectModifierInterface[] $providers
     */
    public function __construct(array $providers = [])
    {
        $this->providers = $providers;
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
        foreach ($this->providers as $provider) {
            $provider->apply($select, $searchCriteria);
        }
    }
}
