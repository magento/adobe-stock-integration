<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Magento\MediaContentSynchronizationApi\Model;

/**
 * Get Entites by provided configuration.
 */
interface GetEntitiesInterface
{
    /**
     * Get entities that used for media content
     *
     * @return array
     */
    public function execute(): array;
}
