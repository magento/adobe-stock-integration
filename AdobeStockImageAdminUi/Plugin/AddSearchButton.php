<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

declare(strict_types=1);

namespace Magento\AdobeStockImageAdminUi\Plugin;

use Magento\AdobeStockImageAdminUi\Model\IsAdobeStockIntegrationEnabled;
use Magento\Backend\Block\Widget\Container;
use Magento\Framework\AuthorizationInterface;
use Magento\Framework\View\LayoutInterface;

/**
 * Plugin which adds an Adobe Stock search button to the media gallery tab
 */
class AddSearchButton
{
    /**
     * Acl for images preview
     */
    private const ACL_SAVE_PREVIEW_IMAGES = 'Magento_AdobeStockImageAdminUi::save_preview_images';

    /**
     * @var AuthorizationInterface
     */
    private $authorization;

    /**
     * @var IsAdobeStockIntegrationEnabled
     */
    private $isAdobeStockIntegrationEnabled;

    /**
     * AddSearchButton constructor.
     *
     * @param IsAdobeStockIntegrationEnabled $isAdobeStockIntegrationEnabled
     * @param AuthorizationInterface $authorization
     */
    public function __construct(
        IsAdobeStockIntegrationEnabled $isAdobeStockIntegrationEnabled,
        AuthorizationInterface $authorization
    ) {
        $this->isAdobeStockIntegrationEnabled = $isAdobeStockIntegrationEnabled;
        $this->authorization = $authorization;
    }

    /**
     * Add Adobe Stock Search button to the toolbar
     *
     * @param Container $subject
     * @param LayoutInterface $layout
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function beforeSetLayout(Container $subject, LayoutInterface $layout): void
    {
        if ($this->authorization->isAllowed(self::ACL_SAVE_PREVIEW_IMAGES)
            && $this->isAdobeStockIntegrationEnabled->execute()
        ) {
            $subject->addButton(
                'search_adobe_stock',
                [
                    'class' => 'action-secondary',
                    'label' => __('Search Adobe Stock'),
                    'type' => 'button',
                    'onclick' => 'jQuery(".adobe-search-images-modal").trigger("openModal");'
                ],
                0,
                0,
                'header'
            );
        }
    }
}
