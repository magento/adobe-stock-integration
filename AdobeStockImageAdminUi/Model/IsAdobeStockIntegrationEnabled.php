<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

declare(strict_types=1);

namespace Magento\AdobeStockImageAdminUi\Model;

use Magento\Framework\App\Config\ScopeConfigInterface;

/**
 * Used for managing an Adobe stock module config functionality
 */
class IsAdobeStockIntegrationEnabled
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
    public function execute(): bool
    {
        return $this->scopeConfig->isSetFlag(self::XML_PATH_ENABLED);
    }
}
