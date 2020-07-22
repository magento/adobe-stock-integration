<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Magento\MediaGalleryMetadataApi\Model;

/**
 * File reader
 */
interface FileReaderInterface
{
    /**
     * Create file object from the file
     *
     * @param string $path
     * @return null|FileInterface
     */
    public function execute(string $path): ?FileInterface;
}
