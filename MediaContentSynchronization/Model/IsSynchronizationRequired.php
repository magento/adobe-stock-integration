<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Magento\MediaContentSynchronization\Model;

use Magento\Framework\Stdlib\DateTime\DateTimeFactory;
use Magento\Framework\FlagManager;
use Magento\MediaContentSynchronizationApi\Model\IsSynchronizationRequiredInterface;

/**
 * Verify is synchronization required for entity
 */
class IsSynchronizationRequired implements IsSynchronizationRequiredInterface
{
    private const LAST_EXECUTION_TIME_CODE = 'media_content_last_execution';
    
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
     */
    public function execute(string $timeField): bool
    {
        $lastExecutionTime = $this->flagManager->getFlagData(self::LAST_EXECUTION_TIME_CODE);

        if (!$lastExecutionTime) {
            return true;
        }
        return $timeField > $lastExecutionTime;
    }
}
