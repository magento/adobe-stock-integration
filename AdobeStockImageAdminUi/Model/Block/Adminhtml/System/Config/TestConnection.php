<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magento\AdobeStockImageAdminUi\Model\Block\Adminhtml\System\Config;

use Magento\AdobeStockAsset\Model\Config;
use Magento\Backend\Block\Template\Context;
use Magento\Config\Block\System\Config\Form\Field;
use Magento\Framework\Data\Form\Element\AbstractElement;
use Magento\Framework\Serialize\Serializer\Json;

/**
 * Adobe Stock test connection block
 * @codeCoverageIgnore
 */
class TestConnection extends Field
{
    /**
     * @var Config
     */
    private $config;
    /**
     * @var Json
     */
    private $json;

    /**
     * TestConnection constructor.
     * @param Context $context
     * @param Json    $json
     * @param Config  $config
     */
    public function __construct(
        Context $context,
        Json $json,
        Config $config
    ) {
        $this->config = $config;
        parent::__construct($context);
        $this->json = $json;
    }

    /**
     * Set template to itself
     *
     * @return TestConnection
     * @since 100.1.0
     */
    protected function _prepareLayout()
    {
        parent::_prepareLayout();
        $this->setTemplate('Magento_AdobeStockImageAdminUi::system/config/testconnection.phtml');
        return $this;
    }

    /**
     * Get the button and scripts contents
     *
     * @param AbstractElement $element
     * @return string
     * @since 100.1.0
     */
    protected function _getElementHtml(AbstractElement $element)
    {
        $originalData = $element->getOriginalData();
        $this->addData(
            [
                'button_label' => __($originalData['button_label']),
                'html_id'      => $element->getHtmlId(),
                'headers'      =>
                    $this->json->serialize(
                        [
                            'x-api-key'                   => $this->config->getApiKey(),
                            'X-Product'                   => $this->config->getProductName(),
                            'Access-Control-Allow-Origin' => '*',
                        ]
                    ),
                'ajax_url'     => 'https://stock.adobe.io/Rest/Media/1/Search/Files?locale=en_US&search_parameters[words]=tree&search_parameters[limit]=1',
            ]
        );

        return $this->_toHtml();
    }
}
