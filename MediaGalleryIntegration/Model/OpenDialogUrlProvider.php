<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Magento\MediaGalleryIntegration\Model;

use Magento\MediaGalleryUiApi\Api\ConfigInterface;

/**
 * Provider to get open media gallery dialog URL for WYSIWYG and widgets
 */
class OpenDialogUrlProvider
{
    /**
     * @var ConfigInterface
     */
    private $config;

    /**
     * @param ConfigInterface $config
     */
    public function __construct(ConfigInterface $config)
    {
        $this->config = $config;
    }

    /**
     * Get Url based on media gallery configuration
     *
     * @return string
     */
    public function getUrl(): string
    {
        return $this->config->isEnabled() ? 'media_gallery/index/index' :  'cms/wysiwyg_images/index';
    }
}
