<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

declare(strict_types=1);

namespace Magento\AdobeStockImageAdminUi\Ui\Component\Listing\Columns\ContentType;

use Magento\Framework\Data\OptionSourceInterface;

/**
 * Content Type Photo filter options provider
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
                'value' => 'photo',
                'label' => __('Photo')
            ],
            [
                'value' => 'illustration',
                'label' => __('Illustration')
            ]
        ];
    }
}
