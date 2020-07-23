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
     * @param FileInterfaceFactory $fileFactory
     * @param array[] $metadataWriters
     */
    public function __construct(
        FileInterfaceFactory $fileFactory,
        array $metadataWriters
    ) {
        $this->fileFactory = $fileFactory;
        $this->metadataWriters = $metadataWriters;
    }

    /**
     * @inheritdoc
     */
    public function execute(string $path, MetadataInterface $metadata): void
    {
        foreach ($this->metadataWriters as $writer) {
            $file = $this->readFile($writer['fileReaders'], $path);
            
            if (!empty($file->getSegments())) {
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

    /**
     * Read file by given fileReaders
     *
     * @param array $fileReaders
     * @param string $path
     */
    private function readFile(array $fileReaders, string $path): FileInterface
    {
        $file =  $this->fileFactory->create([
            'path' => $path,
            'segments' => []
        ]);

        foreach ($fileReaders as $fileReader) {
            try {
                $file = $fileReader->execute($path);
            } catch (ValidatorException $exception) {
                continue;
            } catch (\Exception $exception) {
                throw new LocalizedException(
                    __('Could not parse the image file for metadata: %path', ['path' => $path])
                );
            }
        }
        return $file;
    }
}
