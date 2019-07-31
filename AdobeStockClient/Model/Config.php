<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

declare(strict_types=1);

namespace Magento\AdobeStockClient\Model;

use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\UrlInterface;

/**
 * Class Config
 */
class Config
{
    private const XML_PATH_API_KEY = 'adobe_stock/integration/api_key';
    private const XML_PATH_PRIVATE_KEY = 'adobe_stock/integration/private_key';
    private const XML_PATH_ENVIRONMENT = 'adobe_stock/integration/environment';
    private const XML_PATH_PRODUCT_NAME = 'adobe_stock/integration/product_name';
    private const XML_PATH_TOKEN_URL = 'adobe_stock/integration/token_url';
    private const XML_PATH_AUTH_URL_PATTERN = 'adobe_stock/integration/auth_url_pattern';
    private const XML_PATH_LOCALE = 'general/locale/code';

    /**
     * @var ScopeConfigInterface
     */
    private $scopeConfig;

    /**
     * @var array
     */
    private $searchResultFields;

    /**
     * @var UrlInterface
     */
    private $url;

    /**
     * Config constructor.
     * @param ScopeConfigInterface $scopeConfig
     * @param UrlInterface $url
     * @param array $searchResultFields
     */
    public function __construct(
        ScopeConfigInterface $scopeConfig,
        UrlInterface $url,
        array $searchResultFields = []
    ) {
        $this->scopeConfig = $scopeConfig;
        $this->url = $url;
        $this->searchResultFields = $searchResultFields;
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
     * Retrieve integration API private KEY (Client secret)
     *
     * @return string
     */
    public function getPrivateKey(): string
    {
        return (string)$this->scopeConfig->getValue(self::XML_PATH_PRIVATE_KEY);
    }

    /**
     * Environment configuration
     *
     * @return string|null
     */
    public function getTargetEnvironment() : ?string
    {
        return $this->scopeConfig->getValue(self::XML_PATH_ENVIRONMENT);
    }

    /**
     * Product name
     *
     * @return string|null
     */
    public function getProductName() : ?string
    {
        return $this->scopeConfig->getValue(self::XML_PATH_PRODUCT_NAME);
    }

    /**
     * Retrieve token URL
     *
     * @return string
     */
    public function getTokenUrl(): string
    {
        return $this->scopeConfig->getValue(self::XML_PATH_TOKEN_URL);
    }

    /**
     * Retrieve auth URL
     *
     * @return string
     */
    public function getAuthUrl(): string
    {
        return str_replace(
            ['#{client_id}', '#{redirect_uri}', '#{locale}'],
            [$this->getApiKey(), $this->getCallBackUrl(), $this->getLocale()],
            $this->scopeConfig->getValue(self::XML_PATH_AUTH_URL_PATTERN)
        );
    }

    /**
     * Search result configuration
     *
     * @return array|string[]
     */
    public function getSearchResultFields(): array
    {
        return $this->searchResultFields;
    }

    /**
     * Retrieve Callback URL
     *
     * @return string
     */
    public function getCallBackUrl(): string
    {
        return $this->url->getUrl('adobe_stock/oauth/callback');
    }

    /**
     * Retrieve token URL
     *
     * @return string
     */
    public function getLocale(): string
    {
        return $this->scopeConfig->getValue(self::XML_PATH_LOCALE);
    }
}
