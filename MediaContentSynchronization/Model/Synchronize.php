<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Magento\MediaContentSynchronization\Model;

use Magento\MediaContentSynchronizationApi\Api\SynchronizeInterface;
use Magento\MediaContentSynchronizationApi\Model\SynchronizerPool;
use Magento\Framework\Exception\LocalizedException;
use Psr\Log\LoggerInterface;

/**
 * Synchronize content with assets
 */
class Synchronize implements SynchronizeInterface
{
    /**
     * @var LoggerInterface
     */
    private $log;

    /**
     * @var SynchronizerPool
     */
    private $synchronizerPool;

    /**
     * @param LoggerInterface $log
     * @param SynchronizerPool $synchronizerPool
     */
    public function __construct(
        LoggerInterface $log,
        SynchronizerPool $synchronizerPool
    ) {
        $this->log = $log;
        $this->synchronizerPool = $synchronizerPool;
    }

    /**
     * @inheritdoc
     */
    public function execute(): void
    {
        $failed = [];

        foreach ($this->synchronizerPool->get() as $name => $synchronizer) {
            try {
                $synchronizer->execute();
            } catch (\Exception $exception) {
                $this->log->critical($exception);
                $failed[] = $name;
            }
        }

        if (!empty($failed)) {
            throw new LocalizedException(
                __(
                    'Failed to execute the following content synchronizers: %synchronizers',
                    [
                        'synchronizers' => implode(', ', $failed)
                    ]
                )
            );
        }
    }
}
