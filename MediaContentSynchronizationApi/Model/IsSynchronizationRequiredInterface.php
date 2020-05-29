<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Magento\MediaContentSynchronizationApi\Model;

/**
 * Verify if synchronization must be executed for the entity
 */
interface IsSynchronizationRequiredInterface
{
    /**
     * Verify if need to execute synchronization for entity
     *
     * @param string $timeField
     * @param string $lastExecutionTimeFlagCode
     */
    public function execute(string $timeField, string $lastExecutionTimeFlagCode): bool;
}
