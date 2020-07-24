<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Magento\MediaGalleryMetadata\Model\File;

use Magento\Framework\Exception\LocalizedException;
use Magento\MediaGalleryMetadataApi\Api\Data\MetadataInterface;
use Magento\MediaGalleryMetadataApi\Model\FileInterface;
use Magento\Framework\Exception\ValidatorException;
use Magento\MediaGalleryMetadataApi\Api\AddMetadataInterface;
use Magento\MediaGalleryMetadataApi\Model\FileInterfaceFactory;
use Magento\MediaGalleryMetadataApi\Model\ReadFileInterface;
use Magento\MediaGalleryMetadataApi\Model\WriteFileInterface;
use Magento\MediaGalleryMetadataApi\Model\WriteMetadataInterface;

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
     * @var FileInterfaceFactory
     */
    private $fileFactory;

    /**
     * @var array
     */
    private $fileReaders;

    /**
     * @var array
     */
    private $fileWriters;

    /**
     * @param FileInterfaceFactory $fileFactory
     * @param array $metadataWriters
     * @param array $fileReaders
     * @param array $fileWriters
     */
    public function __construct(
        FileInterfaceFactory $fileFactory,
        array $metadataWriters,
        array $fileReaders,
        array $fileWriters
    ) {
        $this->fileFactory = $fileFactory;
        $this->fileReaders = $fileReaders;
        $this->fileWriters = $fileWriters;
        $this->metadataWriters = $metadataWriters;
    }

    /**
     * @inheritdoc
     */
    public function execute(string $path, MetadataInterface $metadata): void
    {
        $fileExtension = str_replace('image/', '', getimagesize($path)['mime']);

        if (!$this->isApplicable($fileExtension)) {
            throw new LocalizedException(
                __('File format is not supported: %path', ['path' => $path])
            );
        }

        $file = $this->readFile($this->fileReaders[$fileExtension], $path);
        
        try {
            $this->writeFile(
                $this->writeMetadata($file, $this->metadataWriters[$fileExtension], $metadata),
                $this->fileWriters[$fileExtension]
            );
        } catch (\Exception $exception) {
            throw new LocalizedException(
                __('Could not update the image file metadata: %path', ['path' => $path])
            );
        }
    }

    /**
     * Is file applicable to add metadata
     *
     * @param string $fileExtension
     */
    private function isApplicable(string $fileExtension): bool
    {
        return isset($this->fileReaders[$fileExtension]) ||
            isset($this->metadataWriters[$fileExtension]) ||
            isset($this->fileWriters[$fileExtension]);
    }
    
    /**
     * Read file by given path
     *
     * @param ReadFileInterface $reader
     * @param string $path
     */
    private function readFile(ReadFileInterface $reader, string $path): FileInterface
    {
        try {
            $file = $reader->execute($path);
        } catch (\Exception $exception) {
            throw new LocalizedException(
                __('Could not parse the image file for metadata: %path', ['path' => $path])
            );
        }
        return $file;
    }

    /**
     * Write metadata by given metadata writer
     *
     * @param FileInterface $file
     * @param array $metadataWriters
     */
    private function writeMetadata(
        FileInterface $file,
        array $metadataWriters,
        MetadataInterface $metadata
    ): FileInterface {
        foreach ($metadataWriters as $writer) {
            if (!$writer instanceof WriteMetadataInterface) {
                throw new LocalizedException(__('SegmentWriter must implement WriteMetadataInterface'));
            }

            $file = $writer->execute($file, $metadata);
        }
        return $file;
    }

    /**
     * Write file
     *
     * @param FileInterface $file
     */
    private function writeFile(FileInterface $file, WriteFileInterface $writer): void
    {
        $writer->execute($file);
    }
}
