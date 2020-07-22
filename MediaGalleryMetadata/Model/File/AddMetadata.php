<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Magento\MediaGalleryMetadata\Model\File;

use Magento\Framework\Exception\FileSystemException;
use Magento\Framework\Exception\LocalizedException;
use Magento\MediaGalleryMetadataApi\Api\AddMetadataInterface;
use Magento\MediaGalleryMetadataApi\Api\Data\MetadataInterface;
use Magento\MediaGalleryMetadataApi\Model\FileInterface;
use Magento\MediaGalleryMetadataApi\Model\FileReaderInterface;
use Magento\MediaGalleryMetadataApi\Model\FileWriterInterface;
use Magento\MediaGalleryMetadataApi\Model\MetadataWriterInterface;

/**
 * Write metadata to the asset file for all supportet types e.g IPTC, XMP ...
 */
class AddMetadata implements AddMetadataInterface
{
    /**
     * @var array
     */
    private $metadataWriters;

    /**
     * @param array[] $metadataWriters
     */
    public function __construct(
        array $metadataWriters
    ) {
        $this->metadataWriters = $metadataWriters;
    }

    /**
     * @inheritdoc
     */
    public function execute(string $path, MetadataInterface $metadata): void
    {
        foreach ($this->metadataWriters as $writer) {
            try {
                foreach ($writer['fileReaders'] as $fileReader) {
                    $file = $fileReader->execute($path);
                }
            } catch (\Exception $exception) {
                throw new LocalizedException(
                    __('Could not parse the image file for metadata: %path', ['path' => $path])
                );
            }

            if (!empty($file)) {
                try {
                    foreach ($writer['segmentWriters'] as $segmentWriter) {
                        $file = $segmentWriter->execute($file, $metadata);
                    }
                    foreach ($writer['fileWriters'] as $fileWriter) {
                        $fileWriter->execute($file);
                    }
                } catch (\Exception $exception) {
                    throw new LocalizedException(
                        __('Could not update the image file metadata: %path', ['path' => $path])
                    );
                }
            }
        }
    }
}
