<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Magento\MediaGalleryIntegration\Plugin;

use Magento\Framework\UrlInterface;
use Magento\Ui\Component\Form\Element\DataType\Media\Image;
use Magento\MediaGalleryUiApi\Api\ConfigInterface;

/**
 * Plugin to update open media gallery dialog URL for image-uploader component
 */
class UpdateOpenDialogUrl
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
    public function __construct(UrlInterface $url, ConfigInterface $config)
    {
        $this->url = $url;
        $this->config = $config;
    }

    /**
     * Update open media gallery dialog URL for image-uploader component
     *
     * @param Image $component
     */
    public function afterPrepare(Image $component): void
    {
        if (!$this->config->isEnabled()) {
            return;
        }

        $data = $component->getData();

        $data['config']['mediaGallery']['options']['is_new_media_gallery'] = 1;
        $data['config']['mediaGallery']['openDialogUrl'] = $this->url->getUrl('media_gallery/index/index');

        $component->setData($data);
    }
}
