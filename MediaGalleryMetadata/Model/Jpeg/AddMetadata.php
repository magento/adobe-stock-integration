<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Magento\MediaGalleryMetadata\Model\Jpeg;

use Magento\MediaGalleryMetadataApi\Api\AddMetadataInterface;
use Magento\MediaGalleryMetadataApi\Api\Data\MetadataInterface;
use Magento\MediaGalleryMetadataApi\Model\MetadataWriterInterface;

/**
 * Add metadata to the asset by path
 */
class AddMetadata implements AddMetadataInterface
{
    /**
     * @var MetadataWriterInterface[]
     */
    private $writers;

    /**
     * @var FileReader
     */
    private $fileReader;

    /**
     * @var FileWriter
     */
    private $fileWriter;

    /**
     * @param FileReader $fileReader
     * @param FileWriter $fileWriter
     * @param MetadataWriterInterface[] $writers
     */
    public function __construct(FileReader $fileReader, FileWriter $fileWriter, array $writers)
    {
        $this->fileReader = $fileReader;
        $this->fileWriter = $fileWriter;
        $this->writers = $writers;
    }

    /**
     * @inheritdoc
     */
    public function execute(string $path, MetadataInterface $data): void
    {
        if (!$this->fileReader->isApplicable($path)) {
            return;
        }
        $file = $this->fileReader->execute($path);
        foreach ($this->writers as $writer) {
            $updateFile = $writer->execute($file, $data);
        }
        $this->fileWriter->execute($updateFile);
    }
}
