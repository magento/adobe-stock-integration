<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Magento\MediaIndexer\Model;

use Magento\MediaContentSynchronizationApi\Api\SynchronizeInterface;
use Magento\Framework\Exception\LocalizedException;
use Psr\Log\LoggerInterface;

/**
 * Consumer of the media content synchronization queue.
 */
class Content
{
    /**
     * @var SynchronizeInterface
     */
    private $contentSynchronization;

    /**
     * @var LoggerInterface
     */
    private $logger;

    /**
     * Content constructor.
     *
     * @param SynchronizeInterface $contentSynchronization
     * @param LoggerInterface $logger
     */
    public function __construct(SynchronizeInterface $contentSynchronization, LoggerInterface $logger)
    {
        $this->contentSynchronization = $contentSynchronization;
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
            $this->contentSynchronization->execute();
        } catch (\Exception $exception) {
            $this->logger->critical($exception);
        }
    }
}
