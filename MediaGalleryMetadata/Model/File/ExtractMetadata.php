<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Magento\MediaGalleryMetadata\Model\File;

use Magento\MediaGalleryMetadataApi\Api\Data\MetadataInterface;
use Magento\MediaGalleryMetadataApi\Api\Data\MetadataInterfaceFactory;
use Magento\MediaGalleryMetadataApi\Api\ExtractMetadataInterface;
use Magento\MediaGalleryMetadataApi\Model\FileReaderInterface;
use Magento\MediaGalleryMetadataApi\Model\MetadataReaderInterface;

/**
 * Extract metadata from the asset by path. Should be used as a virtual type with a file type specific configuration
 */
class ExtractMetadata implements ExtractMetadataInterface
{
    /**
     * @var FileReaderInterface
     */
    private $fileReader;

    /**
     * @var MetadataReaderInterface[]
     */
    private $readers;

    /**
     * @var MetadataInterfaceFactory
     */
    private $metadataFactory;

    /**
     * @param FileReaderInterface $fileReader
     * @param MetadataInterfaceFactory $metadataFactory
     * @param MetadataReaderInterface[] $readers
     */
    public function __construct(
        FileReaderInterface $fileReader,
        MetadataInterfaceFactory $metadataFactory,
        array $readers
    ) {
        $this->readers = $readers;
        $this->fileReader = $fileReader;
        $this->metadataFactory = $metadataFactory;
    }

    /**
     * @inheritdoc
     */
    public function execute(string $path): MetadataInterface
    {
        if (!$this->fileReader->isApplicable($path)) {
            return $this->getEmptyResult();
        }

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
            'title' => '',
            'description' => '',
            'keywords' => []
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
        $title = '';
        $description = '';
        $keywords = [];
        $file = $this->fileReader->execute($path);
        foreach ($this->readers as $reader) {
            $data = $reader->execute($file);
            $title = $data->getTitle() ?? $title;
            $description = $data->getDescription() ?? $description;
            // phpcs:ignore Magento2.Performance.ForeachArrayMerge
            $keywords = array_merge($keywords, $data->getKeywords());
            if (!empty($title) && !empty($description) && !empty($keywords)) {
                break;
            }
        }
        return $this->metadataFactory->create([
            'title' => $title,
            'description' => $description,
            'keywords' => array_unique($keywords)
        ]);
    }
}
