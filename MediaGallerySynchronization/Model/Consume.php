<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Magento\MediaGallerySynchronization\Model;

use Magento\MediaGallerySynchronizationApi\Api\SynchronizeInterface;
use Psr\Log\LoggerInterface;

/**
 * Media gallery image synchronization queue consumer.
 */
class Consume
{
    /**
     * @var SynchronizeInterface
     */
    private $imagesIndexer;

    /**
     * @var LoggerInterface
     */
    private $logger;

    /**
     * SynchronizationConsumer constructor.
     *
     * @param SynchronizeInterface $imagesIndexer
     * @param LoggerInterface $logger
     */
    public function __construct(SynchronizeInterface $imagesIndexer, LoggerInterface $logger)
    {
        $this->imagesIndexer = $imagesIndexer;
        $this->logger = $logger;
    }

    /**
     * Run media files synchronization.
     */
    public function execute() : void
    {
        try {
            $this->imagesIndexer->execute();
        } catch (\Exception $exception) {
            $this->logger->critical($exception);
        }
    }
}
