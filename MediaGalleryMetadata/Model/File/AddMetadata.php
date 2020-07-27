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
 * Add metadata to the asset by path. Should be used as a virtual type with a file type specific configuration
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
     * @var ReadFileInterface
     */
    private $fileReader;

    /**
     * @var WriteFileInterface
     */
    private $fileWriter;

    /**
     * @param FileInterfaceFactory $fileFactory
     * @param ReadFileInterface $fileReader
     * @param WriteFileInterface $fileWriter
     * @param array $metadataWriters
     */
    public function __construct(
        FileInterfaceFactory $fileFactory,
        ReadFileInterface $fileReader,
        WriteFileInterface $fileWriter,
        array $metadataWriters
    ) {
        $this->fileFactory = $fileFactory;
        $this->fileReader = $fileReader;
        $this->fileWriter = $fileWriter;
        $this->metadataWriters = $metadataWriters;
    }

    /**
     * @inheritdoc
     */
    public function execute(string $path, MetadataInterface $metadata): void
    {
        try {
            $file = $this->fileReader->execute($path);
        } catch (ValidatorException $e) {
            return;
        } catch (\Exception $exception) {
            throw new LocalizedException(
                __('Could not parse the image file for metadata: %path', ['path' => $path])
            );
        }

        try {
            $this->fileWriter->execute($this->writeMetadata($file, $metadata));
        } catch (\Exception $exception) {
            throw new LocalizedException(
                __('Could not update the image file metadata: %path', ['path' => $path])
            );
        }
    }

    /**
     * Write metadata by given metadata writer
     *
     * @param FileInterface $file
     * @param MetadataInterface $metadata
     */
    private function writeMetadata(FileInterface $file, MetadataInterface $metadata): FileInterface
    {
        foreach ($this->metadataWriters as $writer) {
            if (!$writer instanceof WriteMetadataInterface) {
                throw new \InvalidArgumentException(
                    __(get_class($writer) . ' must implement '. WriteFileInterface::class)
                );
            }

            $file = $writer->execute($file, $metadata);
        }
        return $file;
    }
}
