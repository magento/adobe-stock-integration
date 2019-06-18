<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magento\AdobeStockAsset\Model\System\Message\Notification;

use Magento\AdobeStockAssetApi\Api\Data\ConfigInterface;
use Magento\Framework\Notification\MessageInterface;
use Magento\Framework\UrlInterface;

class NotConfigured implements MessageInterface
{
    const MESSAGE_IDENTITY = 'adobe_stock_system_message';
    /**
     * @var UrlInterface
     */
    private $urlBuilder;
    /**
     * @var ConfigInterface
     */
    private $config;

    /**
     * CustomMessage constructor.
     * @param ConfigInterface $config
     * @param UrlInterface    $urlBuilder
     */
    public function __construct(
        ConfigInterface $config,
        UrlInterface $urlBuilder
    ) {
        $this->urlBuilder = $urlBuilder;
        $this->config = $config;
    }

    /**
     * Retrieve unique message identity
     *
     * @return string
     */
    public function getIdentity()
    {
        return self::MESSAGE_IDENTITY;
    }

    /**
     * Check whether should show or not
     *
     * @return bool
     */
    public function isDisplayed()
    {
        return $this->config->isEnabled() && !$this->config->getApiKey();
    }

    /**
     * Retrieve message text
     *
     * @return string
     */
    public function getText()
    {
        $url = $this->urlBuilder->getUrl('adminhtml/system_config/edit/section/system');
        return __('Adobe Stock API not configured. Please, proceed to <a href="%1">Configuration → System → Adobe Stock Integration</a>', $url);
    }

    /**
     * Retrieve message severity
     *
     * @return int
     */
    public function getSeverity()
    {
        return self::SEVERITY_MAJOR;
    }
}
