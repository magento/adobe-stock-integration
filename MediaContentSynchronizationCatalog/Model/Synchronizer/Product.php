<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Magento\MediaContentSynchronizationCms\Model\Synchronizer;

use Magento\MediaContentSynchronizationApi\Api\SynchronizerInterface;
use Psr\Log\LoggerInterface;

/**
 * Synchronize content with assets
 */
class Product implements SynchronizerInterface
{
    /**
     * @var LoggerInterface
     */
    private $log;

    /**
     * @param LoggerInterface $log
     */
    public function __construct(
        LoggerInterface $log
    ) {
        $this->log = $log;
    }

    /**
     * @inheritdoc
     */
    public function execute(): void
    {
        //implementation
    }
}
