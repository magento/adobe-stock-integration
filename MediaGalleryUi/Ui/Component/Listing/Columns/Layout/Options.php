<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Magento\MediaGalleryUi\Ui\Component\Listing\Columns\Layout;

use Magento\Framework\Data\OptionSourceInterface;
use Magento\Framework\Option\ArrayInterface;

/**
 * Image source filter options
 */
class Options implements ArrayInterface
{
    /**
     * @inheritdoc
     */
    public function toOptionArray(): array
    {
        return [
            [
                'value' => 'cms_page',
                'label' =>  __('Pages'),
            ],
            [
                'value' => 'catalog_category',
                'label' =>  __('Categories'),
            ],
            [
                'value' => 'cms_block',
                'label' =>  __('Blocks'),
            ],
            [
                'value' => 'catalog_product',
                'label' =>  __('Products'),
            ],
        ];
    }
}
