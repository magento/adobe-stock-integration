<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Magento\AdobeStockAdminUi\Block\Adminhtml;

use Magento\AdobeIms\Model\Config;
use Magento\Backend\Block\Template;
use Magento\Backend\Block\Template\Context;

/**
 * Adobe Stock sign in block
 */
class ImsConfig extends Template
{
    /**
     * @var Config
     */
    private $config;

    /**
     * ImsConfig constructor.
     * @param Config $config
     * @param Context $context
     * @param array $data
     */
    public function __construct(
        Config $config,
        Context $context,
        array $data = []
    ) {
        parent::__construct($context, $data);
        $this->config = $config;
    }

    /**
     * Return IMS authentication URL
     *
     * @return string
     */
    public function getAuthUrl(): string
    {
        return $this->config->getAuthUrl();
    }
}
