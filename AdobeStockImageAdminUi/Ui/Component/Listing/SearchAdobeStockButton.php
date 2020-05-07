<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Magento\AdobeStockImageAdminUi\Ui\Component\Listing;

use Magento\AdobeStockImageAdminUi\Model\IsAdobeStockIntegrationEnabled;
use Magento\Framework\View\Element\UiComponent\Control\ButtonProviderInterface;

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
     * @param IsAdobeStockIntegrationEnabled $isAdobeStockIntegrationEnabled
     */
    public function __construct(
        IsAdobeStockIntegrationEnabled $isAdobeStockIntegrationEnabled
    ) {
        $this->isAdobeStockIntegrationEnabled = $isAdobeStockIntegrationEnabled;
    }

    /**
     * @inheritdoc
     */
    public function getButtonData()
    {
        if ($this->isAdobeStockIntegrationEnabled->execute()) {
            return [];
        }

        return [
            'label' => __('Search Adobe Stock'),
            'sort_order' => '100',
            'class' => 'media-gallery-actions-buttons',
            'on_click' => 'jQuery(".adobe-search-images-modal").trigger("openModal");'
        ];
    }
}
