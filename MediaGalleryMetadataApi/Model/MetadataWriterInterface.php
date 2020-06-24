<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Magento\MediaGalleryMetadataApi\Model;

use Magento\MediaGalleryMetadataApi\Api\Data\MetadataInterface;

/**
 * Metadata writer interface
 */
interface MetadataWriterInterface
{
    /**
     * Add metadata to the file
     *
     * @param string $path
     * @param MetadataInterface $data
     */
    public function execute(string $path, MetadataInterface $data): void;
}
