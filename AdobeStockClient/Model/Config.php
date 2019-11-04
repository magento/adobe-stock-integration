<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

declare(strict_types=1);

namespace Magento\AdobeStockClient\Model;

use Magento\AdobeStockClientApi\Api\ConfigInterface;
use Magento\Config\Model\Config\Backend\Admin\Custom;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\App\ProductMetadataInterface;
use Magento\Framework\UrlInterface;

/**
 * Class Config
 */
class Config implements ConfigInterface
{
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
     * @var UrlInterface
     */
    private $url;

    /**
     * @var ProductMetadataInterface
     */
    private $metadataInterface;

    /**
     * Constructor
     *
     * @param ScopeConfigInterface $scopeConfig
     * @param UrlInterface $url
     * @param ProductMetadataInterface $metadataInterface
     * @param array $searchResultFields
     */
    public function __construct(
        ScopeConfigInterface $scopeConfig,
        UrlInterface $url,
        ProductMetadataInterface $metadataInterface,
        array $searchResultFields = []
    ) {
        $this->scopeConfig = $scopeConfig;
        $this->url = $url;
        $this->searchResultFields = $searchResultFields;
        $this->metadataInterface = $metadataInterface;
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
        return $this->scopeConfig->getValue(self::XML_PATH_PRODUCT_NAME) . '/' . $this->metadataInterface->getVersion();
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
     * Retrieve token URL
     *
     * @return string
     */
    public function getLocale(): string
    {
        return $this->scopeConfig->getValue(Custom::XML_PATH_GENERAL_LOCALE_CODE);
    }
}
