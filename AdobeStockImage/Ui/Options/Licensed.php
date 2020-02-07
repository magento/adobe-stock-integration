<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Magento\AdobeStockImage\Ui\Options;

use Magento\Framework\Data\OptionSourceInterface;

/**
 * Licensed status filter options
 */
class Licensed implements OptionSourceInterface
{
    /**
     * @inheritdoc
     */
    public function toOptionArray(): array
    {
        return [
            [
                'value' => '1',
                'label' => 'Licensed'
            ],
            [
                'value' => '0',
                'label' => 'Unlicensed'
            ]
        ];
    }
}
