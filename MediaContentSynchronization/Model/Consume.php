<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Magento\MediaContentSynchronization\Model;

use Magento\MediaContentSynchronizationApi\Api\SynchronizeInterface;
use Psr\Log\LoggerInterface;

/**
 * Media content synchronization queue consumer.
 */
class Consume
{
    /**
     * @var SynchronizeInterface
     */
    private $synchronize;

    /**
     * @var LoggerInterface
     */
    private $logger;

    /**
     * SynchronizationConsumer constructor.
     *
     * @param SynchronizeInterface $synchronize
     * @param LoggerInterface $logger
     */
    public function __construct(SynchronizeInterface $synchronize, LoggerInterface $logger)
    {
        $this->synchronize = $synchronize;
        $this->logger = $logger;
    }

    /**
     * Run media files synchronization.
     */
    public function execute() : void
    {
        try {
            $this->synchronize->execute();
        } catch (\Exception $exception) {
            $this->logger->critical($exception);
        }
    }
}
