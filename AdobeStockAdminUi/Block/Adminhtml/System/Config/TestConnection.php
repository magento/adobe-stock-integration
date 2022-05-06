<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

declare(strict_types=1);

namespace Magento\AdobeStockAdminUi\Block\Adminhtml\System\Config;

use Magento\AdminAdobeIms\Service\ImsConfig;
use Magento\AdobeImsApi\Api\ConfigInterface;
use Magento\AdobeStockClientApi\Api\ClientInterface;
use Magento\Backend\Block\Template\Context;
use Magento\Config\Block\System\Config\Form\Field;
use Magento\Framework\App\ObjectManager;
use Magento\Framework\Data\Form\Element\AbstractElement;

/**
 * Adobe Stock test connection block
 */
class TestConnection extends Field
{
    private const TEST_CONNECTION_PATH = 'adobe_stock/system_config/testconnection';

    /**
     * @var string
     */
    protected $_template = 'Magento_AdobeStockAdminUi::system/config/connection.phtml';

    /**
     * @var ClientInterface
     */
    private $client;

    /**
     * @var ConfigInterface
     */
    private $config;

    /**
     * @var ImsConfig
     */
    private ImsConfig $adminAdobeImsConfig;

    /**
     * @param ClientInterface $client
     * @param ConfigInterface $config
     * @param Context $context
     * @param array $data
     * @param ImsConfig|null $adminAdobeImsConfig
     */
    public function __construct(
        ClientInterface $client,
        ConfigInterface $config,
        Context $context,
        array $data = [],
        ?ImsConfig $adminAdobeImsConfig = null
    ) {
        $this->client = $client;
        $this->config = $config;
        $this->adminAdobeImsConfig = $adminAdobeImsConfig
            ?? ObjectManager::getInstance()->get(ImsConfig::class);
        parent::__construct($context, $data);
    }

    /**
     * Remove element scope and render form element as HTML
     *
     * @param AbstractElement $element
     * @return string
     */
    public function render(AbstractElement $element): string
    {
        // render Test Connection button only if Admin Adobe IMS is disabled
        if (!$this->adminAdobeImsConfig->enabled()) {
            $element->setData('scope', null);
            return parent::render($element);
        }

        return '';
    }

    /**
     * Get the button and scripts contents
     *
     * @param AbstractElement $element
     * @return string
     */
    protected function _getElementHtml(AbstractElement $element): string
    {
        $this->addData(
            [
                'button_label' => __($element->getOriginalData()['button_label']),
            ]
        );

        return $this->_toHtml();
    }

    /**
     * Check connection for the current api key saved value.
     *
     * @return bool
     */
    public function isConnectionSuccessful(): bool
    {
        if ($this->config->getApiKey()) {
            return $this->client->testConnection($this->config->getApiKey());
        }

        return true;
    }

    /**
     * Get test connection url
     *
     * @return string
     */
    public function getAjaxUrl(): string
    {
        return $this->_urlBuilder->getUrl(
            self::TEST_CONNECTION_PATH,
            [
                'form_key' => $this->getFormKey(),
            ]
        );
    }
}
