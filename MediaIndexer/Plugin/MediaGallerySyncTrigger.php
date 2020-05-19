<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Magento\MediaIndexer\Plugin;

use Magento\MediaIndexer\Indexer\Content;
use Magento\MediaIndexer\Indexer\Image;
use Magento\Framework\App\Config\Value;

/**
 * Plugin to synchronize media storage and media assets database recoders when media gallery enabled in configuration.
 */
class MediaGallerySyncTrigger
{
    private const MEDIA_GALLERY_CONFIG_VALUE = 'system/media_gallery/enabled';
    private const MEDIA_GALLERY_ENABLED_VALUE = 1;

    /**
     * @var Content
     */
    private $contentSyncQueuePublisher;

    /**
     * @var Image
     */
    private $imageSyncQueuePublisher;

    /**
     * MediaGallerySyncTrigger constructor.
     *
     * @param Content $contentSyncQueuePublisher
     * @param Image $imageSyncQueuePublisher
     */
    public function __construct(
        Content $contentSyncQueuePublisher,
        Image $imageSyncQueuePublisher
    ) {
        $this->contentSyncQueuePublisher = $contentSyncQueuePublisher;
        $this->imageSyncQueuePublisher = $imageSyncQueuePublisher;
    }

    /**
     * Update media gallery index by publishing synchronization tasks into queue.
     *
     * @param Value $config
     * @param Value $result
     * @return Value
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function afterSave(Value $config, Value $result): Value
    {
        if ($result->getPath() === self::MEDIA_GALLERY_CONFIG_VALUE
            && $result->isValueChanged()
            && (int) $result->getValue() === self::MEDIA_GALLERY_ENABLED_VALUE
        ) {
            $this->imageSyncQueuePublisher->execute();
            $this->contentSyncQueuePublisher->execute();
        }

        return $result;
    }
}
