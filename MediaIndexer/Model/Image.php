<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Magento\MediaIndexer\Model;

use Magento\MediaGallerySynchronizationApi\Api\SynchronizeInterface;
use Magento\Framework\Exception\LocalizedException;
use Psr\Log\LoggerInterface;

/**
 * Consumer of the media file synchronization queue.
 */
class Image
{
    /**
     * @var SynchronizeInterface
     */
    private $meidaFilesSynchronization;

    /**
     * @var LoggerInterface
     */
    private $logger;

    /**
     * Image constructor.
     *
     * @param SynchronizeInterface $meidaFilesSynchronization
     * @param LoggerInterface $logger
     */
    public function __construct(SynchronizeInterface $meidaFilesSynchronization, LoggerInterface $logger)
    {
        $this->meidaFilesSynchronization = $meidaFilesSynchronization;
        $this->logger = $logger;
    }

    /**
     * Run media content synchronization.
     *
     * @throws LocalizedException
     */
    public function execute() : void
    {
        try {
            $this->meidaFilesSynchronization->execute();
        } catch (\Exception $exception) {
            $this->logger->critical($exception);
        }
    }
}
