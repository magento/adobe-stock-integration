<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

declare(strict_types=1);

namespace Magento\AdobeStockAsset\Model\Block\Adminhtml\System\Config;

use Magento\AdobeStockClient\Model\Config;
use Magento\AdobeStockClientApi\Api\ClientInterface;
use Magento\Config\Block\System\Config\Form\Field;
use Magento\Backend\Block\Template\Context;
use Magento\Framework\Data\Form\Element\AbstractElement;

/**
 * Adobe Stock test connection block
 */
class TestConnection extends Field
{
    /**
     * @inheritdoc
     */
    protected $_template = 'Magento_AdobeStockAsset::system/config/testconnection.phtml';

    /**
     * @var ClientInterface
     */
    private $client;

    /**
     * @var Config
     */
    private $config;

    /**
     * TestConnection constructor.
     *
     * @param ClientInterface $client
     * @param Config          $config
     * @param Context         $context
     * @param array           $data
     */
    public function __construct(
        ClientInterface $client,
        Config $config,
        Context $context,
        array $data = []
    ) {
        $this->client = $client;
        $this->config = $config;
        parent::__construct($context, $data);
    }

    /**
     * Remove element scope and render form element as HTML
     *
     * @param AbstractElement $element
     * @return string
     */
    public function render(AbstractElement $element)
    {
        $element->setData('scope', null);
        return parent::render($element);
    }

    /**
     * Get the button and scripts contents
     *
     * @param AbstractElement $element
     * @return string
     */
    protected function _getElementHtml(AbstractElement $element)
    {
        $originalData = $element->getOriginalData();
        $this->addData(
            [
                'button_label' => __($originalData['button_label']),
                'html_id' => $element->getHtmlId(),
                'ajax_url' => $this->_urlBuilder->getUrl(
                    'adobe_stock/system_config/testconnection',
                    [
                        'form_key' => $this->getFormKey()
                    ]
                ),
                'api_key_validation_result_container_id' => 'validation_api_connection_result',
                'is_current_connection_valid' => $this->isConnectionValid(),
            ]
        );

        return $this->_toHtml();
    }

    /**
     * Check connection for the current api key saved value.
     *
     * @return bool
     */
    private function isConnectionValid(): bool
    {
        $apiKey = $this->config->getApiKey();
        $isConnectionValid = $this->client->testConnection($apiKey);

        return $isConnectionValid;
    }
}
