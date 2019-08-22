<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

declare(strict_types=1);

namespace Magento\AdobeStockImageAdminUi\Block\Adminhtml;

use Magento\AdobeIms\Model\Config;
use Magento\Backend\Block\Template;
use Magento\Backend\Block\Template\Context;

/**
 * Adobe Stock sign in block
 */
class Panel extends Template
{

    /**
     * @var Config
     */
    private $config;

    /**
     * Panel constructor.
     *
     * @param Config $config
     * @param Context $context
     * @param array $data
     */
    public function __construct(
        Config $config,
        Context $context,
        array $data = []
    ) {
        $this->config = $config;
        parent::__construct($context, $data);
    }

    /**
     * Return auth url for adobe stock.
     *
     * @return string
     */
    public function getAuthUrl(): string
    {
        return $this->config->getAuthUrl();
    }
}
