<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Magento\MediaContentSynchronization\Plugin;

use Magento\MediaContentSynchronization\Model\Synchronize;
use Magento\Framework\Exception\LocalizedException;
use \Magento\MediaGallerySynchronization\Model\SynchronizationConsumer;
use Psr\Log\LoggerInterface;

/**
 * Run media content synchronization after the media files consumer finish files index.
 */
class SynchronizeMediaContent
{
    /**
     * @var Synchronize
     */
    private $mediaContentIndexer;

    /**
     * @var LoggerInterface
     */
    private $logger;

    /**
     * SynchronizeMediaContent constructor.
     *
     * @param Synchronize $mediaContentIndexer
     * @param LoggerInterface $logger
     */
    public function __construct(Synchronize $mediaContentIndexer, LoggerInterface $logger)
    {
        $this->mediaContentIndexer = $mediaContentIndexer;
        $this->logger = $logger;
    }

    /**
     * Run media content synchronization.
     *
     * @param SynchronizationConsumer $subject
     * @param null $result
     *
     * @return void
     * @throws LocalizedException
     */
    public function afterProcess(SynchronizationConsumer $subject, $result)
    {
        try {
            $this->mediaContentIndexer->execute();
        } catch (\Exception $exception) {
            $this->logger->critical($exception);
        }

        return $result;
    }
}

