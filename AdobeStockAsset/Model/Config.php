<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Magento\AdobeStockAsset\Model;

use Magento\Framework\App\Config\ScopeConfigInterface;

/**
 * Class Config
 */
class Config
{
    private const XML_PATH_ENABLED = 'adobe_stock/integration/enabled';

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
    public function isEnabled(): bool
    {
        return (bool) $this->scopeConfig->getValue(self::XML_PATH_ENABLED);
    }
}
