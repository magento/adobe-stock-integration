<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Magento\MediaGalleryMetadata\Model\Png;

use Magento\MediaGalleryMetadataApi\Api\ExtractMetadataInterface;
use Magento\MediaGalleryMetadataApi\Api\Data\MetadataInterface;
use Magento\MediaGalleryMetadataApi\Api\Data\MetadataInterfaceFactory;
use Magento\MediaGalleryMetadataApi\Model\MetadataReaderInterface;

/**
 * Extract metadata from the asset by path
 */
class ExtractMetadata implements ExtractMetadataInterface
{
    /**
     * @var FileReader
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
     * @param FileReader $fileReader
     * @param MetadataInterfaceFactory $metadataFactory
     * @param MetadataReaderInterface[] $readers
     */
    public function __construct(
        FileReader $fileReader,
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
        $title = '';
        $description = '';
        $keywords = [];
        if (!$this->fileReader->isApplicable($path)) {
            return $this->metadataFactory->create([
                'title' => $title,
                'description' => $description,
                'keywords' => $keywords
            ]);
        }
        $file = $this->fileReader->execute($path);
        foreach ($this->readers as $reader) {
            $data = $reader->execute($file);
            $title = $data->getTitle() ?? $title;
            $description = $data->getDescription() ?? $description;
            $keywords = array_unique(array_merge($keywords, $data->getKeywords()));
            if (!empty($title) && !empty($description) && !empty($keywords)) {
                break;
            }
        }
        return $this->metadataFactory->create([
            'title' => $title,
            'description' => $description,
            'keywords' => $keywords
        ]);
    }
}
