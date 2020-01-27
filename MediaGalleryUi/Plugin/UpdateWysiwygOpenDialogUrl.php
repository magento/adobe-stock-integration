<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Magento\MediaGalleryUi\Plugin;

use Magento\Framework\DataObject;
use Magento\Framework\UrlInterface;
use Magento\Cms\Model\Wysiwyg\Config;
use Magento\Cms\Helper\Wysiwyg\Images;
use Magento\MediaGalleryUiApi\Api\ConfigInterface;
use Magento\Cms\Model\Wysiwyg\Gallery\DefaultConfigProvider;
use Magento\MediaGalleryUi\Model\Config as MediaGalleryConfig;

/**
 * Plugin to update open media gallery dialog URL for WYSIWYG
 */
class UpdateWysiwygOpenDialogUrl
{
    /**
     * @var UrlInterface
     */
    private $url;

    /**
     * @var Images
     */
    private $imagesHelper;

    /**
     * @var ConfigInterface
     */
    private $config;

    /**
     * @param UrlInterface $url
     */
    public function __construct(UrlInterface $url, Images $imagesHelper, ConfigInterface $config)
    {
        $this->url = $url;
        $this->imagesHelper = $imagesHelper;
        $this->config = $config;
    }

    /**
     * Update open media gallery dialog URL for WYSIWYG
     *
     * @param DefaultConfigProvider $provider
     * @param DataObject $config
     * @return DataObject
     */
    public function afterGetConfig(DefaultConfigProvider $provider, DataObject $config): DataObject
    {
        if (!$this->config->isEnabled()) {
            return $config;
        }

        return $config->setData(
            'files_browser_window_url',
            $this->url->getUrl(
                MediaGalleryConfig::MEDIA_GALLERY_INDEX_ROUTE_PATH,
                [
                    'current_tree_path' => $this->imagesHelper->idEncode(Config::IMAGE_DIRECTORY)
                ]
            )
        );
    }
}
