<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magento\AdobeStockImageAdminUi\Model\Block\Wysiwyg\Images\Content\Plugin;

use Magento\Cms\Block\Adminhtml\Wysiwyg\Images\Content;

/**
 * Plugin for media gallery block adding button to the toolbar.
 */
class AddSearchButton
{
    public function __construct(\Magento\AdobeStockImage\Model\Image $getImageList)
    {

    }

    /**
     * Add Adobe Stock Search button to the toolbar
     *
     * @param Content $subject
     */
    public function beforeSetLayout(Content $subject)
    {
        $subject->addButton(
            'search_adobe_stock',
            [
                'class' => 'action-secondary',
                'label' => __('Search Adobe Stock'),
                'type' => 'button',
                'onclick' => 'jQuery("#adobe-stock-images-search-modal").trigger("openModal");'
            ],
            0,
            0,
            'header'
        );
    }
}
