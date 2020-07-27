<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Magento\MediaGalleryMetadataApi\Model;

use Magento\Framework\Exception\LocalizedException;
use Magento\MediaGalleryMetadataApi\Api\Data\MetadataInterface;
use Magento\MediaGalleryMetadataApi\Api\ExtractMetadataInterface;

/**
 * Metadata extractor composite
 */
class ExtractMetadataComposite
{
    /**
     * @var ExtractMetadataInterface[]
     */
    private $writers;

    /**
     * @param ExtractMetadataInterface[] $extractors
     */
    public function __construct(array $extractors)
    {
        $this->extractors = $extractors;
    }

    /**
     * Extract metadata from file
     *
     * @param string $path
     * @return MetadataInterface
     * @throws LocalizedException
     */
    public function execute(string $path): MetadataInterface
    {
        foreach ($this->extractors as $extractor) {
            $metadata = $extractor->execute($path);
            if ($metadata->getTitle() !== null ||
                $metadata->getDescription() !== null ||
                $metadata->getKeywords() !== null) {
                break;
            }
        }
        return $metadata;
    }
}
