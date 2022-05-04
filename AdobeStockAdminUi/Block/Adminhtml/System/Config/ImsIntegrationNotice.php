<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Magento\AdobeStockAdminUi\Block\Adminhtml\System\Config;

use Magento\AdminAdobeIms\Service\ImsConfig;
use Magento\Backend\Block\Template\Context;
use Magento\Config\Block\System\Config\Form\Field;
use Magento\Framework\Data\Form\Element\AbstractElement;
use Magento\Framework\View\Helper\SecureHtmlRenderer;

/**
 * Renderer of notice for IMS integration for Adobe Stock
 */
class ImsIntegrationNotice extends Field
{
    /**
     * @var ImsConfig
     */
    private ImsConfig $adminAdobeImsConfig;

    /**
     * Template path
     *
     * @var string
     */
    protected $_template = 'Magento_AdobeStockAdminUi::system/config/ims_integration_notice.phtml';

    /**
     * @param Context $context
     * @param ImsConfig $adminAdobeImsConfig
     * @param array $data
     * @param SecureHtmlRenderer|null $secureRenderer
     */
    public function __construct(
        Context $context,
        ImsConfig $adminAdobeImsConfig,
        array $data = [],
        ?SecureHtmlRenderer $secureRenderer = null
    ) {
        $this->adminAdobeImsConfig = $adminAdobeImsConfig;
        parent::__construct($context, $data, $secureRenderer);
    }

    /**
     * Render field html
     *
     * @param AbstractElement $element
     * @return string
     */
    public function render(AbstractElement $element)
    {
        if (!$this->adminAdobeImsConfig->enabled()) {
            return $this->_decorateRowHtml($element, "<td colspan='4'>" . $this->toHtml() . '</td>');
        }

        return '';
    }
}
