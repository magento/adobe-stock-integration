<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Magento\AdobeStockImageAdminUi\Model\Block\Wysiwyg\Images\Content\Plugin;

use Magento\Backend\Block\Widget\Container;
use Magento\AdobeStockAsset\Model\Config;
use Magento\Framework\View\LayoutInterface;

/**
 * Plugin for media gallery block adding button to the toolbar.
 */
class AddSearchButton
{
    /**
     * @var Config
     */
    private $config;

    /**
     * AddSearchButton constructor.
     * @param Config $config
     */
    public function __construct(Config $config)
    {
        $this->config = $config;
    }

    /**
     * Add Adobe Stock Search button to the toolbar
     *
     * @param Container $subject
     * @return null
     *
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function beforeSetLayout(Container $subject, LayoutInterface $layout)
    {
        if ($this->config->isEnabled()) {
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
}
