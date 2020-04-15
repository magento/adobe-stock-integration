<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Magento\MediaGallerySynchronization\Plugin;

use Magento\MediaGallerySynchronizationApi\Api\SynchronizeInterface;
use Magento\Framework\App\Config\Value;

/**
 * Plugin to synchronize media storage and media assets database recoders when media gallery enabled in configuration
 */
class MediaGallerySyncTrigger
{
    private const MEDIA_GALLERY_CONFIG_VALUE = 'system/media_gallery/enabled';
    private const MEDIA_GALLERY_ENABLED_VALUE = 1;

    /**
     * @var SynchronizeInterface
     */
    private $imagesIndexer;

    /**
     * @param SynchronizeInterface $imagesIndexer
     */
    public function __construct(SynchronizeInterface $imagesIndexer)
    {
        $this->imagesIndexer = $imagesIndexer;
    }

    /**
     * Update media gallery grid table when configuration is saved and media gallery enabled
     *
     * @param Value $config
     * @param Value $result
     * @return Value
     * @throws \Magento\Framework\Exception\LocalizedException
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function afterSave(Value $config, Value $result): Value
    {
        if ($result->getPath() === self::MEDIA_GALLERY_CONFIG_VALUE
            && $result->isValueChanged()
            && (int) $result->getValue() === self::MEDIA_GALLERY_ENABLED_VALUE
        ) {
            $this->imagesIndexer->execute();
        }

        return $result;
    }
}
