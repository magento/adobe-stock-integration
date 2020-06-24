<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Magento\MediaGalleryMetadataApi\Model;

/**
 * Metadata reader pool
 */
class ReaderPool
{
    /**
     * @var MetadataReaderInterface[]
     */
    private $readers;

    /**
     * @param MetadataReaderInterface[] $readers
     */
    public function __construct(array $readers)
    {
        $this->readers = $readers;
    }

    /**
     * @return MetadataReaderInterface[]
     */
    public function get(): array
    {
        return $this->readers;
    }

}
