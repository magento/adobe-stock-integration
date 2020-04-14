<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Magento\AdobeStockImageAdminUi\Ui\Component\Listing\Columns\Source;

use Magento\Framework\Data\OptionSourceInterface;

/**
 * Image source filter options
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
                'value' => 'Adobe Stock',
                'label' =>  __('Adobe Stock'),
            ],
            [
                'value' => 'Local',
                'label' =>  __('Uploaded Locally'),
            ],
        ];
    }
}
