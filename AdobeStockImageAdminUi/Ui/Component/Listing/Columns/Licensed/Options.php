<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Magento\AdobeStockImageAdminUi\Ui\Component\Listing\Columns\Licensed;

use Magento\Framework\Data\OptionSourceInterface;

/**
 * Licensed status filter options
 */
class Options implements OptionSourceInterface
{
    /**
     * @inheritdoc
     */
    public function toOptionArray(): array
    {
        return [
            [
                'value' => '1',
                'label' =>  __('Licensed')
            ],
            [
                'value' => '0',
                'label' =>  __('Unlicensed')
            ]
        ];
    }
}
