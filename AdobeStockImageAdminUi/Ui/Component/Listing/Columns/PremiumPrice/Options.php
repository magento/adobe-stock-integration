<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

declare(strict_types=1);

namespace Magento\AdobeStockImageAdminUi\Ui\Component\Listing\Columns\PremiumPrice;

use Magento\Framework\Data\OptionSourceInterface;

/**
 * Pricing filter options provider
 */
class Options implements OptionSourceInterface
{
    /**
     * Get options
     *
     * @return array
     */
    public function toOptionArray() : array
    {
        return [
            [
                'label' => __('Standard'),
                'value' => 'FALSE'
            ],
            [
                'label' => __('Premium'),
                'value' => 'TRUE'
            ]
        ];
    }
}
