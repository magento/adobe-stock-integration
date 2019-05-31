<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magento\AdobeStockImageAdminUi\Ui\Component\Listing\Columns\Orientation;

/**
 * Orientation filter options provider
 */
class Options implements \Magento\Framework\Data\OptionSourceInterface
{
    /**
     * Get options
     *
     * @return array
     */
    public function toOptionArray()
    {
        return [
            [
                'label' => 'Horizontal',
                'value' => 'HORIZONTAL'
            ],
            [
                'label' => 'Vertical',
                'value' => 'VERTICAL'
            ],
            [
                'label' => 'Square',
                'value' => 'SQUARE'
            ],
            [
                'label' => 'All',
                'value' => 'ALL'
            ]
        ];
    }
}
