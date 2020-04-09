<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Magento\MediaGalleryIntegration\Plugin;

use Magento\Framework\UrlInterface;
use Magento\Framework\DataObject;
use Magento\MediaGalleryUiApi\Api\ConfigInterface;

/**
 * Plugin to update open media gallery dialog URL for image-uploader component
 */
class UpdateWysiwygOpenDialogUrlTinyMce
{
    /**
     * @var UrlInterface
     */
    private $url;

    /**
     * @var ConfigInterface
     */
    private $config;
    
    /**
     * @param UrlInterface $url
     * @param ConfigInterface $config
     */
    public function __construct(
        UrlInterface $url,
        ConfigInterface $config
    ) {
        $this->url = $url;
        $this->config = $config;
    }

    /**
     * Update open media gallery dialog URL for image-uploader component
     *
     * @param Image $component
     */
    public function afterPrepare(DataObject $config): void
    {
        if (!$this->config->isEnabled()) {
            return;
        }
        $config->setData('files_browser_window_url', $this->url->getUrl('media_gallery/index/index'));
    }
}
