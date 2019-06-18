<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

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
     * @{inheritdoc}
     */
    public function isEnabled() : bool
    {
        return (bool) $this->scopeConfig->getValue(self::XML_PATH_ENABLED);
    }

    /**
     * @{inheritdoc}
     */
    public function getApiKey() : string
    {
        return $this->scopeConfig->getValue(self::XML_PATH_API_KEY) ?: '';
    }

    /**
     * @{inheritdoc}
     */
    public function getTargetEnvironment() : string
    {
        return $this->scopeConfig->getValue(self::XML_PATH_ENVIRONMENT) ?: '';
    }

    /**
     * @{inheritdoc}
     */
    public function getProductName() : string
    {
        return $this->scopeConfig->getValue(self::XML_PATH_PRODUCT_NAME) ?: '';
    }
}
