<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

declare(strict_types=1);

namespace Magento\MediaGallerySynchronizationApi\Api;

use Magento\Framework\Exception\LocalizedException;

/**
 * Save file data
 */
interface ImportFileInterface
{
    /**
     * Save file data
     *
     * @param string $path
     * @throws LocalizedException
     */
    public function execute(string $path): void;
}
