<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Magento\AdobeStockImageAdminUi\Ui\Component\Listing;

use Magento\AdobeStockImageAdminUi\Model\IsAdobeStockIntegrationEnabled;
use Magento\Framework\View\Element\UiComponent\Control\ButtonProviderInterface;
use Magento\Framework\AuthorizationInterface;

/**
 * Adobe Stock Search Button
 */
class SearchAdobeStockButton implements ButtonProviderInterface
{
    /**
     * @var IsAdobeStockIntegrationEnabled
     */
    private $isAdobeStockIntegrationEnabled;

    /**
     * Acl for images preview
     */
    private const ACL_SAVE_PREVIEW_IMAGES = 'Magento_AdobeStockImageAdminUi::save_preview_images';

    /**
     * @var AuthorizationInterface
     */
    private $authorization;
  
    /**
     * @param AuthorizationInterface $authorization
     * @param IsAdobeStockIntegrationEnabled $isAdobeStockIntegrationEnabled
     */
    public function __construct(
        AuthorizationInterface $authorization,
        IsAdobeStockIntegrationEnabled $isAdobeStockIntegrationEnabled
    ) {
        $this->authorization = $authorization;
        $this->isAdobeStockIntegrationEnabled = $isAdobeStockIntegrationEnabled;
    }

    /**
     * @inheritdoc
     */
    public function getButtonData()
    {
        if (!$this->isAllowed()) {
            return [];
        }

        return [
            'label' => __('Search Adobe Stock'),
            'sort_order' => '100',
            'class' => 'media-gallery-actions-buttons',
            'on_click' => 'jQuery(".adobe-search-images-modal").trigger("openModal");'
        ];
    }

    /**
     * Verify if  Adobe Stock Search button allowed
     */
    private function isAllowed(): bool
    {
        return $this->isAdobeStockIntegrationEnabled->execute() &&
            $this->authorization->isAllowed(self::ACL_SAVE_PREVIEW_IMAGES);
    }
}
