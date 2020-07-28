<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Magento\MediaGalleryUi\Ui\Component\Listing\Filters\Options;

use Magento\Framework\Data\OptionSourceInterface;

/**
 * Used in filter options
 */
class UsedIn implements OptionSourceInterface
{
    /**
     * @var array
     */
    private $options;

    /**
     * @param array $options
     */
    public function __construct(array $options = [])
    {
        $this->options = $options;
    }

    /**
     * @inheritdoc
     */
    public function toOptionArray(): array
    {
        return $this->usedInOptions();
    }

    public function usedInOptions()
    {
        $this->options = [
            'cms_page' => [
                'value' => 'cms_page',
                'label' => 'Pages'
            ],
            'catalog_category' => [
                'value' => 'catalog_category',
                'label' => 'Categories'
            ],
            'cms_block' => [
                'value' => 'cms_block',
                'label' => 'Blocks'
            ],
            'catalog_product' => [
                'value' => 'catalog_product',
                'label' => 'Products'
            ],
            'not_used' => [
                'value' => 'not_used',
                'label' => 'Not used anywhere'
            ]
        ];

        return $this->options;
    }
}
