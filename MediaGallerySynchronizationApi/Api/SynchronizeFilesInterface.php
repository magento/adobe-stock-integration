<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Magento\MediaGallerySynchronizationApi\Api;

/**
 * Synchronize assets from the provided files information to database
 */
interface SynchronizeFilesInterface
{
    /**
     * Create media gallery assets based on files information and save them to database
     *
     * @param \SplFileInfo[] $items
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function execute(array $items): void;
}
