<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

declare(strict_types=1);

namespace Magento\AdobeStockClient\Model;

use Magento\Framework\App\Config\ScopeConfigInterface;

/**
 * Class Config
 */
class Config
{
    private const XML_PATH_API_KEY = 'adobe_stock/integration/api_key';
    private const XML_PATH_PRIVATE_KEY = 'adobe_stock/integration/private_key';
    private const XML_PATH_ENVIRONMENT = 'adobe_stock/integration/environment';
    private const XML_PATH_PRODUCT_NAME = 'adobe_stock/integration/product_name';

    /**
     * @var ScopeConfigInterface
     */
    private $scopeConfig;

    /**
     * @var array
     */
    private $searchResultFields;

    /**
     * Config constructor.
     * @param ScopeConfigInterface $scopeConfig
     * @param array $searchResultFields
     */
    public function __construct(ScopeConfigInterface $scopeConfig, array $searchResultFields = [])
    {
        $this->scopeConfig = $scopeConfig;
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
     * Search result configuration
     *
     * @return array|string[]
     */
    public function getSearchResultFields(): array
    {
        return $this->searchResultFields;
    }
}
