<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

declare(strict_types=1);

namespace Magento\AdobeStockImageAdminUi\Ui\Component\Listing\Columns\Orientation;

use Magento\Framework\Data\OptionSourceInterface;

/**
 * Orientation filter options provider
 */
class Options implements OptionSourceInterface
{
    /**
     * Get options
     *
     * @return array
     */
    public function toOptionArray(): array
    {
        return [
            [
                'label' => __('Horizontal'),
                'value' => 'HORIZONTAL'
            ],
            [
                'label' => __('Vertical'),
                'value' => 'VERTICAL'
            ],
            [
                'label' => __('Square'),
                'value' => 'SQUARE'
            ],
            [
                'label' => __('Panoramic'),
                'value' => 'PANORAMIC'
            ],
        ];
    }
}
