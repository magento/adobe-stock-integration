<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

declare(strict_types=1);

namespace Magento\AdobeIms\Model;

use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\UrlInterface;
use Magento\Config\Model\Config\Backend\Admin\Custom;
use Magento\AdobeImsApi\Api\Data\ConfigInterface;

/**
 * Class Config
 */
class Config implements ConfigInterface
{
    private const XML_PATH_API_KEY = 'adobe_stock/integration/api_key';
    private const XML_PATH_PRIVATE_KEY = 'adobe_stock/integration/private_key';
    private const XML_PATH_TOKEN_URL = 'adobe_stock/integration/token_url';
    private const XML_PATH_AUTH_URL_PATTERN = 'adobe_stock/integration/auth_url_pattern';
    private const XML_PATH_SCOPE_TYPE = 'adobe_stock/integration/scope_type';

    /**
     * @var ScopeConfigInterface
     */
    private $scopeConfig;

    /**
     * @var UrlInterface
     */
    private $url;

    /**
     * Config constructor.
     * @param ScopeConfigInterface $scopeConfig
     * @param UrlInterface $url
     */
    public function __construct(
        ScopeConfigInterface $scopeConfig,
        UrlInterface $url
    ) {
        $this->scopeConfig = $scopeConfig;
        $this->url = $url;
    }

    /**
     * Retrieve integration API key (Client ID)
     *
     * @return string|null
     */
    public function getApiKey():? string
    {
        return $this->scopeConfig->getValue(self::XML_PATH_API_KEY);
    }

    /**
     * @inheritdoc
     */
    public function getPrivateKey(): string
    {
        return (string)$this->scopeConfig->getValue(self::XML_PATH_PRIVATE_KEY);
    }

    /**
     * @inheritdoc
     */
    public function getTokenUrl(): string
    {
        return $this->scopeConfig->getValue(self::XML_PATH_TOKEN_URL);
    }

    /**
     * @inheritdoc
     */
    public function getAuthUrl(): string
    {
        return str_replace(
            ['#{client_id}', '#{redirect_uri}', '#{locale}', '#{scope}'],
            [$this->getApiKey(), $this->getCallBackUrl(), $this->getLocale(), $this->getScope()],
            $this->scopeConfig->getValue(self::XML_PATH_AUTH_URL_PATTERN)
        );
    }

    /**
     * Retrieve scope.
     */
    private function getScope(): string
    {
        return $this->scopeConfig->getValue(self::XML_PATH_SCOPE_TYPE);
    }

    /**
     * Retrieve Callback URL
     *
     * @return string
     */
    public function getCallBackUrl(): string
    {
        return $this->url->getUrl('adobe_ims/oauth/callback');
    }


    /**
     * Retrieve token URL
     *
     * @return string
     */
    private function getLocale(): string
    {
        return $this->scopeConfig->getValue(Custom::XML_PATH_GENERAL_LOCALE_CODE);
    }
}
