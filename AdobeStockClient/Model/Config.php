<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

declare(strict_types=1);

namespace Magento\AdobeStockClient\Model;

use Magento\AdobeStockClientApi\Api\ConfigInterface;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\App\ProductMetadataInterface;

/**
 * Used for managing the Adobe Stock integration config settings
 */
class Config implements ConfigInterface
{
    private const XML_PATH_ENVIRONMENT = 'adobe_stock/integration/environment';
    private const XML_PATH_PRODUCT_NAME = 'adobe_stock/integration/product_name';
    private const XML_PATH_FILES_URL = 'adobe_stock/integration/files_url';

    /**
     * @var ScopeConfigInterface
     */
    private $scopeConfig;

    /**
     * @var ProductMetadataInterface
     */
    private $metadataInterface;

    /**
     * Constructor
     *
     * @param ScopeConfigInterface $scopeConfig
     * @param ProductMetadataInterface $metadataInterface
     */
    public function __construct(
        ScopeConfigInterface $scopeConfig,
        ProductMetadataInterface $metadataInterface
    ) {
        $this->scopeConfig = $scopeConfig;
        $this->metadataInterface = $metadataInterface;
    }

    /**
     * @inheritdoc
     */
    public function getTargetEnvironment(): ?string
    {
        return $this->scopeConfig->getValue(self::XML_PATH_ENVIRONMENT);
    }

    /**
     * @inheritdoc
     */
    public function getProductName(): ?string
    {
        return $this->scopeConfig->getValue(self::XML_PATH_PRODUCT_NAME) . '/' . $this->metadataInterface->getVersion();
    }

    /**
     * @inheritdoc
     */
    public function getFilesUrl(): string
    {
        return $this->scopeConfig->getValue(self::XML_PATH_FILES_URL);
    }
}
