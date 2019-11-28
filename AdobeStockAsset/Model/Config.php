<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

declare(strict_types=1);

namespace Magento\AdobeStockAsset\Model;

use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\AdobeStockAssetApi\Api\ConfigInterface;

/**
 * Used for managing an Adobe stock module config functionality
 */
class Config implements ConfigInterface
{
    /**
     * Path to enable/disable adobe stock integration in the system settings.
     */
    private const XML_PATH_ENABLED = 'adobe_stock/integration/enabled';

    /**
     * @var ScopeConfigInterface
     */
    private $scopeConfig;

    /**
     * Config constructor.
     *
     * @param ScopeConfigInterface $scopeConfig
     */
    public function __construct(ScopeConfigInterface $scopeConfig)
    {
        $this->scopeConfig = $scopeConfig;
    }

    /**
     * Is integration enabled
     *
     * @return bool
     */
    public function isEnabled(): bool
    {
        return $this->scopeConfig->isSetFlag(self::XML_PATH_ENABLED);
    }
}
