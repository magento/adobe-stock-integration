<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Magento\MediaGallerySynchronizationApi\Model;

/**
 * Interface for file encryption.
 *
 * @package Magento\MediaGallerySynchronizationApi\Model
 */
interface FileEncryptionInterface
{
    /**
     * Hash the given string.
     *
     * @param string $filepath
     * @return string
     */
    public function hash(string $filepath): string;
}
