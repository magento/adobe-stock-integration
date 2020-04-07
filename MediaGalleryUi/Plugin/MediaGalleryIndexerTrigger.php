<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Magento\MediaGalleryUi\Plugin;

use Magento\MediaGalleryUi\Model\ImagesIndexer;
use Magento\Framework\App\Config\Value;

/**
 * Plugin to update media gallery grid table when media gallery enabled in configuration
 */
class MediaGalleryIndexerTrigger
{
    private const MEDIA_GALLERY_CONFIG_VALUE = 'system/media_gallery/enabled';
    private const MEDIA_GALLERY_ENABLED_VALUE = 1;

    /**
     * @var ImagesIndexer
     */
    private $imagesIndexer;

    /**
     * @param ImagesIndexer $imagesIndexer
     */
    public function __construct(
        ImagesIndexer $imagesIndexer
    ) {
        $this->imagesIndexer = $imagesIndexer;
    }

    /**
     * Update media gallery grid table when configuration is saved and media gallery enabled
     *
     * @param Value $config
     * @param Value $result
     * @return Value
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function afterSave(Value $config, Value $result): Value
    {
        $isMediaGallery = $result->getPath() == self::MEDIA_GALLERY_CONFIG_VALUE;
        if ($isMediaGallery && $result->isValueChanged() && $result->getValue() == self::MEDIA_GALLERY_ENABLED_VALUE) {
            $this->imagesIndexer->execute();
        }

        return $result;
    }
}
