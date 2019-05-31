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
use Magento\Store\Model\StoreManagerInterface;

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
     * @var StoreManagerInterface
     */
    private $storeManager;

    /**
     * TestConnection constructor.
     * @param Context               $context
     * @param Json                  $json
     * @param StoreManagerInterface $storeManager
     * @param Config                $config
     */
    public function __construct(
        Context $context,
        Json $json,
        StoreManagerInterface $storeManager,
        Config $config
    ) {
        $this->config = $config;
        $this->json = $json;
        $this->storeManager = $storeManager;
        parent::__construct($context);
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
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     * @since 100.1.0
     */
    protected function _getElementHtml(AbstractElement $element)
    {
        $originalData = $element->getOriginalData();
        $this->addData(
            [
                'button_label' => __($originalData['button_label']),
                'html_id'      => $element->getHtmlId(),
                'headers'      => $this->json->serialize(
                    [
                        'x-api-key'                   => $this->config->getApiKey(),
                        'X-Product'                   => $this->config->getProductName(),
                        'Access-Control-Allow-Origin' => '*',
                    ]
                ),
                'ajax_url'     => $this->getAjaxUrl(),
            ]
        );

        return $this->_toHtml();
    }

    /**
     * @return string
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function getAjaxUrl(): string
    {
        $restUrl = 'rest/V1/adobe-stock/image-list';
        $unsecureUrl = str_replace('https', 'http', $this->getBaseUrl());
        return $unsecureUrl . $restUrl . '?searchCriteria[pageSize]=1&searchCriteria[current_page]=1';
    }
}
