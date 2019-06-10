<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magento\AdobeStockImageAdminUi\Ui\Component\Listing\Columns\Price;

/**
 * Pricing filter options provider
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
                'label' => 'All',
                'value' => 'ALL'
            ],
            [
                'label' => 'Standard',
                'value' => 'FALSE'
            ],
            [
                'label' => 'Premium',
                'value' => 'TRUE'
            ]
        ];
    }
}
