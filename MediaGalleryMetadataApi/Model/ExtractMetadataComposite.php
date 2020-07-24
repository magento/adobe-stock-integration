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
class ExtractMetadataComposite
{
    /**
     * @var ExtractMetadataInterface[]
     */
    private $extractors;

    /**
     * @param ExtractMetadataInterface[] $extractors
     */
    public function __construct(array $readers)
    {
        $this->readers = $readers;
    }

    /**
     * Extract metadata from the asset by path
     *
     * @param string $path
     * @throws LocalizedException
     */
    public function execute(string $path): void
    {
        foreach ($this->extractors as $extractor) {
            $extractor->execute($path);
        }
    }
}
