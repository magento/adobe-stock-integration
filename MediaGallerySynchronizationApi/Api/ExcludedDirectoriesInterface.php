<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

declare(strict_types=1);

namespace Magento\MediaGallerySynchronizationApi\Api;

/**
 * Used to manage what directories should be excluded during media gallery asset synchronization.
 */
interface ExcludedDirectoriesInterface
{
    /**
     * Check if the path is excluded from displaying in the media gallery
     *
     * @param string $path
     * @return bool
     */
    public function isExcluded(string $path): bool;
}
