<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Magento\MediaGalleryMetadataApi\Model;

use Magento\MediaGalleryMetadataApi\Api\ExtractMetadataInterface;

/**
 * Metadata reader pool
 */
class ReaderPool
{
    /**
     * @var ExtractMetadataInterface[]
     */
    private $readers;

    /**
     * @param ExtractMetadataInterface[] $readers
     */
    public function __construct(array $readers)
    {
        $this->readers = $readers;
    }

    /**
     * @return ExtractMetadataInterface[]
     */
    public function get(): array
    {
        return $this->readers;
    }

}
