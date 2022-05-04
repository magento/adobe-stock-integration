<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Magento\AdobeStockImageAdminUi\Plugin;

use Magento\AdminAdobeIms\Block\Adminhtml\SignIn;
use Magento\AdminAdobeIms\Service\ImsConfig;
use Magento\Framework\Serialize\Serializer\JsonHexTag;

/**
 * Plugin that manages visibility of Sign Out button
 */
class AdobeStockSignOutButtonPlugin
{
    /**
     * JsonHexTag Serializer Instance
     *
     * @var JsonHexTag
     */
    private $serializer;

    /**
     * @var ImsConfig
     */
    private ImsConfig $adminAdobeImsConfig;

    /**
     * @param JsonHexTag $serializer
     * @param ImsConfig $adminAdobeImsConfig
     */
    public function __construct(
        JsonHexTag $serializer,
        ImsConfig $adminAdobeImsConfig
    ) {
        $this->serializer = $serializer;
        $this->adminAdobeImsConfig = $adminAdobeImsConfig;
    }

    /**
     * Define visibility for Sign Out button if Admin Adobe IMS is enabled or not
     *
     * @param SignIn $getImageDetailsByAssetId
     * @param string $config
     * @return string
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function afterGetComponentJsonConfig(
        SignIn $getImageDetailsByAssetId,
        string $config
    ): string {
        $configData = $this->serializer->unserialize($config);

        if ($this->adminAdobeImsConfig->enabled()) {
            $configData['user']['showSignOutButton'] = false;
        } else {
            $configData['user']['showSignOutButton'] = true;
        }

        return $this->serializer->serialize($configData);
    }
}
