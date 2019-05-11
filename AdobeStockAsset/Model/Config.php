<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magento\AdobeStockAsset\Model;

use Magento\AdobeStockAssetApi\Api\Data\ConfigInterface;
use Magento\Framework\App\Config\ScopeConfigInterface;

/**
 * Class Config
 */
class Config implements ConfigInterface
{
    private const XML_PATH_ENABLED = 'adobe_stock/integration/enabled';
    private const XML_PATH_API_KEY = 'adobe_stock/integration/api_key';
    private const XML_PATH_ENVIRONMENT = 'adobe_stock/integration/environment';
    private const XML_PATH_PRODUCT_NAME = 'adobe_stock/integration/product_name';

    /**
     * @var ScopeConfigInterface
     */
    private $scopeConfig;

    /**
     * Config constructor.
     * @param ScopeConfigInterface $scopeConfig
     */
    public function __construct(ScopeConfigInterface $scopeConfig)
    {
        $this->scopeConfig = $scopeConfig;
    }

    /**
     * @return bool
     */
    public function isEnabled()
    {
        return (bool) $this->scopeConfig->getValue(self::XML_PATH_ENABLED);
    }

    /**
     * @return string
     */
    public function getApiKey()
    {
        return $this->scopeConfig->getValue(self::XML_PATH_API_KEY);
    }

    /**
     * @return string
     */
    public function getTargetEnvironment()
    {
        return $this->scopeConfig->getValue(self::XML_PATH_ENVIRONMENT);
    }

    /**
     * @return string
     */
    public function getProductName()
    {
        return $this->scopeConfig->getValue(self::XML_PATH_PRODUCT_NAME);
    }
}
