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
use Magento\Framework\Stdlib\DateTime\DateTimeFactory;
use Magento\Framework\FlagManager;
use Magento\MediaContentSynchronizationApi\Model\IsSynchronizationRequiredInterface;

/**
 * Synchronize content with assets
 */
class IsSynchronizationRequired implements IsSynchronizationRequiredInterface
{
    /**
     * @var DateTimeFactory
     */
    private $dateFactory;
    
    /**
     * @var FlagManager
     */
    private $flagManager;
    
    /**
     * @param DateTimeFactory $dateFactory
     * @param FlagManager $flagManager
     */
    public function __construct(
        DateTimeFactory $dateFactory,
        FlagManager $flagManager
    ) {
        $this->dateFactory = $dateFactory;
        $this->flagManager = $flagManager;
    }

    /**
     * Check if synchronization can be executed for entity
     *
     * @param string $timeField
     * @param string $lastExecutionTimeFlagCode
     */
    public function execute(string $timeField, string $lastExecutionTimeFlagCode): bool
    {
        $lastExecutionTime = $this->flagManager->getFlagData($lastExecutionTimeFlagCode);

        if (!$lastExecutionTime) {
            return true;
        }
        return $timeField > $lastExecutionTime;
    }
}
