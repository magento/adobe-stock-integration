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
use Magento\PageBuilder\Model\Config\ContentType\AdditionalData\Provider\Uploader\OpenDialogUrl;

/**
 * Plugin to update open media gallery dialog URL for image-uploader component
 */
class UpdateOpenDialogUrlPageBuilder
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
     * @param OpenDialogUrl $subject
     * @param array $itemName
     */
    public function afterGetData($subject, array $itemName)
    {
        $newItem = [];
        if ($this->config->isEnabled()) {
            foreach ($itemName as $key => $item) {
                $newItem[$key] = $this->url->getUrl('media_gallery/index/index');
            }
            return $newItem;
        }
        return $itemName;
    }
}
