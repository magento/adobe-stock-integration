<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

declare(strict_types=1);

namespace Magento\AdobeStockAdminUi\Model;

use Magento\Framework\App\Config\ScopeConfigInterface;

/**
 * Used for identification is Adobe Stock integration enabled or not in the system configuration settings
 */
class IsAdobeStockIntegrationEnabled implements IsAdobeStockIntegrationEnabledInterface
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
     * IsAdobeStockIntegrationEnabled constructor.
     *
     * @param ScopeConfigInterface $scopeConfig
     */
    public function __construct(ScopeConfigInterface $scopeConfig)
    {
        $this->scopeConfig = $scopeConfig;
    }

    /**
     * Check is the Adobe Stock integration enabled or not
     *
     * @return bool
     */
    public function checkStatus(): bool
    {
        return $this->scopeConfig->isSetFlag(self::XML_PATH_ENABLED);
    }
}
