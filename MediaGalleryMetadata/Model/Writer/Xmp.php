<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Magento\MediaGalleryMetadata\Model\Writer;

use Magento\MediaGalleryMetadataApi\Api\Data\MetadataInterface;
use Magento\MediaGalleryMetadataApi\Model\FileInterface;
use Magento\MediaGalleryMetadataApi\Model\MetadataWriterInterface;
use Magento\MediaGalleryMetadata\Model\Reader\File as FileReader;
use Magento\MediaGalleryMetadata\Model\Writer\File as FileWriter;

/**
 * XMP Writer
 */
class Xmp implements MetadataWriterInterface
{
    /**
     * @var FileReader
     */
    private $reader;

    /**
     * @var FileWriter
     */
    private $writer;

    /**
     * @param FileReader $reader
     * @param File $writer
     */
    public function __construct(FileReader $reader, FileWriter $writer)
    {
        $this->reader = $reader;
        $this->writer = $writer;
    }

    /**
     * @inheritdoc
     */
    public function execute(string $path, MetadataInterface $metadata): void
    {
        $file = $this->reader->execute($path);
        $updateFile = $this->addMetadata($file, $metadata);
        $this->writer->execute($updateFile);

    }

    /**
     * @param FileInterface $file
     * @param MetadataInterface $metadata
     * @return FileInterface
     */
    private function addMetadata(FileInterface $file, MetadataInterface $metadata)
    {
        // TODO: Implement addMetadata() method.
        return $file;
    }
}
