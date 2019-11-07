<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

declare(strict_types=1);

namespace Magento\AdobeStockImage\Model;

use Magento\AdobeStockImageApi\Api\ConfigInterface;
use Magento\Framework\App\Config\ScopeConfigInterface;

/**
 * Class Config
 */
class Config implements ConfigInterface
{
    /**
     * Config path for default gallery filter value
     */
    private const XML_PATH_DEFAULT_GALLERY_ID_PATH = 'adobe_stock/integration/default_gallery_id';

    /**
     * @var ScopeConfigInterface
     */
    private $scopeConfig;

    /**
     * Config constructor.
     * @param ScopeConfigInterface $scopeConfig
     */
    public function __construct(
        ScopeConfigInterface $scopeConfig
    ) {
        $this->scopeConfig = $scopeConfig;
    }

    /**
     * @inheritdoc
     */
    public function getDefaultGalleryId(): ?string
    {
        return $this->scopeConfig->getValue(self::XML_PATH_DEFAULT_GALLERY_ID_PATH);
    }
}
