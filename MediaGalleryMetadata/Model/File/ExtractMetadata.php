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

/**
 * Extract metadata from the asset by path. Should be used as a virtual type with a file type specific configuration
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
     * @param MetadataInterfaceFactory $metadataFactory
     * @param array $metdataExtractors
     */
    public function __construct(
        MetadataInterfaceFactory $metadataFactory,
        array $metadataExtractors
    ) {
        $this->metadataFactory = $metadataFactory;
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
        foreach ($this->metadataExtractors as $extractor) {
            $title = '';
            $description = '';
            $keywords = [];
            foreach ($extractor['fileReaders'] as $fileReader) {
                $file = $fileReader->execute($path);
            }
            if (!empty($file)) {
                foreach ($extractor['segmentReaders'] as $segmentReader) {
                    $data = $segmentReader->execute($file);
                    $title = $data->getTitle() ?? $title;
                    $description = $data->getDescription() ?? $description;
                    $keywords = $data->getKeywords();
                    if (!empty($title) && !empty($description) && !empty($keywords)) {
                        return $this->metadataFactory->create([
                            'title' => $title,
                            'description' => $description,
                            'keywords' => array_unique($keywords)
                        ]);
                    }
                }
            }
        }
        return $this->metadataFactory->create([
            'title' => $title,
            'description' => $description,
            'keywords' => array_unique($keywords)
        ]);
    }
}
