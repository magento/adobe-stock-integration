<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

declare(strict_types=1);

namespace Magento\AdobeStockImageAdminUi\Ui\Component\Listing\Filter;

use Magento\Ui\Component\Filters\Type\AbstractFilter;

/**
 * Checkbox filter
 */
class Checkbox extends AbstractFilter
{
    public const NAME = 'filter_input';

    /**
     * Prepare component configuration
     *
     * @return void
     */
    public function prepare(): void
    {
        $this->applyFilter();

        parent::prepare();
    }

    /**
     * Apply filter
     *
     * @return void
     */
    protected function applyFilter(): void
    {
        if (isset($this->filterData[$this->getName()])) {
            $value = $this->filterData[$this->getName()];
            $filter = $this->filterBuilder->setConditionType('like')
                ->setField($this->getName())
                ->setValue($value)
                ->create();

            $this->getContext()->getDataProvider()->addFilter($filter);
        }
    }
}
