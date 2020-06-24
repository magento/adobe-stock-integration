<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Magento\MediaGalleryMetadataApi\Model;

use Magento\MediaGalleryMetadataApi\Api\Data\MetadataInterface;

/**
 * Metadata reader interface
 */
interface MetadataReaderInterface
{
    /**
     * Read metadata from the file
     *
     * @param string $path
     * @return MetadataInterface
     */
    public function execute(string $path): MetadataInterface;
}
