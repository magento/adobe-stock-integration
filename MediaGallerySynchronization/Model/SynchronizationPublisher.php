<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Magento\MediaGallerySynchronization\Model;

use Magento\Framework\MessageQueue\PublisherInterface;

/**
 * Publish media gallery queue.
 */
class SynchronizationPublisher
{
    /**
     * Media gallery synchronization queue topic name.
     */
    private const TOPIC_MEDIA_GALLERY_SYNCHRONIZATION = 'media.gallery.indexer';

    /**
     * @var PublisherInterface
     */
    private $publisher;

    /**
     * Image constructor.
     *
     * @param PublisherInterface $publisher
     */
    public function __construct(PublisherInterface $publisher)
    {
        $this->publisher = $publisher;
    }

    /**
     * Publish to the message queue what content type should be index.
     */
    public function process() : void
    {
        $this->publisher->publish(
            self::TOPIC_MEDIA_GALLERY_SYNCHRONIZATION,
            [self::TOPIC_MEDIA_GALLERY_SYNCHRONIZATION]
        );
    }
}
