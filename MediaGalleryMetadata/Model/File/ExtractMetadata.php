<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Magento\MediaGalleryMetadata\Model\File;

use Magento\MediaGalleryMetadataApi\Api\ExtractMetadataInterface;
use Magento\MediaGalleryMetadataApi\Api\Data\MetadataInterface;
use Magento\MediaGalleryMetadataApi\Api\Data\MetadataInterfaceFactory;
use Magento\Framework\Exception\ValidatorException;
use Magento\MediaGalleryMetadataApi\Model\FileInterfaceFactory;
use Magento\MediaGalleryMetadataApi\Model\FileInterface;
use Magento\Framework\Exception\LocalizedException;
use Magento\MediaGalleryMetadataApi\Model\ReadFileInterface;
use Magento\MediaGalleryMetadataApi\Model\ReadMetadataInterface;

/**
 * Extract Metadata from asset fy file by given extractors
 */
class ExtractMetadata implements ExtractMetadataInterface
{

    /**
     * @var MetadataInterfaceFactory
     */
    private $metadataFactory;

    /**
     * @var array
     */
    private $metadataExtractors;

    /**
     * @var array
     */
    private $fileReaders;

    /**
     * @var FileInterfaceFactory
     */
    private $fileFactory;

    /**
     * @param FileInterfaceFactory $fileFactory
     * @param MetadataInterfaceFactory $metadataFactory
     * @param array $fileReaders
     */
    public function __construct(
        FileInterfaceFactory $fileFactory,
        MetadataInterfaceFactory $metadataFactory,
        array $fileReaders,
        array $metadataExtractors
    ) {
        $this->fileFactory = $fileFactory;
        $this->metadataFactory = $metadataFactory;
        $this->fileReaders = $fileReaders;
        $this->metadataExtractors = $metadataExtractors;
    }

    /**
     * @inheritdoc
     */
    public function execute(string $path): MetadataInterface
    {
        try {
            return $this->extractMetadata($path);
        } catch (\Exception $exception) {
            return $this->getEmptyResult();
        }
    }

    /**
     * Create empty metadata object
     *
     * @return MetadataInterface
     */
    private function getEmptyResult(): MetadataInterface
    {
        return $this->metadataFactory->create([
            'title' => null,
            'description' => null,
            'keywords' => null
        ]);
    }

    /**
     * Extract metadata from file
     *
     * @param string $path
     * @return MetadataInterface
     */
    private function extractMetadata(string $path): MetadataInterface
    {
        $fileExtension = str_replace('image/', '', getimagesize($path)['mime']);
        $file = $this->readFile($this->fileReaders[$fileExtension], $path);

        list($title, $description, $keywords) = $this->readSegments(
            $this->metadataExtractors[$fileExtension],
            $file
        );
         
        return $this->metadataFactory->create([
            'title' => $title,
            'description' => $description,
            'keywords' => empty($keywords) ? null : $keywords
        ]);
    }

    /**
     * Read  file segments by given segmentReader
     *
     * @param array $segmentReaders
     * @param FileInterface $file
     */
    private function readSegments(array $segmentReaders, FileInterface $file): array
    {
        $title = null;
        $description = null;
        $keywords = [];
        
        foreach ($segmentReaders as $segmentReader) {
            if (!$segmentReader instanceof ReadMetadataInterface) {
                throw new LocalizedException(__('SegmentReader must implement ReadMetadataInterface'));
            }

            $data = $segmentReader->execute($file);
            $title = !empty($data->getTitle()) ? $data->getTitle() : $title;
            $description = !empty($data->getDescription()) ? $data->getDescription() : $description;
            $keywords =  $keywords + $data->getKeywords();
        }
        return [$title, $description, $keywords];
    }

    /**
     * Read file by given fileReader
     *
     * @param ReadFileInterface $fileReader
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
}
