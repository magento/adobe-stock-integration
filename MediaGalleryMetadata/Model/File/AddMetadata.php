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
 * Add metadata to the asset by path. Should be used as a virtual type with a file type specific configuration
 */
class AddMetadata implements AddMetadataInterface
{
    /**
     * @var MetadataWriterInterface[]
     */
    private $writers;

    /**
     * @var FileReaderInterface
     */
    private $fileReader;

    /**
     * @var FileWriterInterface
     */
    private $fileWriter;

    /**
     * @param FileReaderInterface $fileReader
     * @param FileWriterInterface $fileWriter
     * @param MetadataWriterInterface[] $writers
     */
    public function __construct(FileReaderInterface $fileReader, FileWriterInterface $fileWriter, array $writers)
    {
        $this->fileReader = $fileReader;
        $this->fileWriter = $fileWriter;
        $this->writers = $writers;
    }

    /**
     * @inheritdoc
     */
    public function execute(string $path, MetadataInterface $metadata): void
    {
        if (!$this->fileReader->isApplicable($path)) {
            return;
        }

        try {
            $file = $this->fileReader->execute($path);
        } catch (\Exception $exception) {
            throw new LocalizedException(
                __(
                    'Could not parse the image file for metadata: %path',
                    [
                        'path' => $path
                    ]
                )
            );
        }

        try {
            $this->writeMetadata($file, $metadata);
        } catch (\Exception $exception) {
            throw new LocalizedException(
                __(
                    'Could not update the image file metadata: %path',
                    [
                        'path' => $path
                    ]
                )
            );
        }
    }

    /**
     * Write metadata to the filesystem
     *
     * @param FileInterface $file
     * @param MetadataInterface $metadata
     * @throws LocalizedException
     * @throws FileSystemException
     */
    private function writeMetadata(FileInterface $file, MetadataInterface $metadata): void
    {
        foreach ($this->writers as $writer) {
            $file = $writer->execute($file, $metadata);
        }
        $this->fileWriter->execute($file);
    }
}
