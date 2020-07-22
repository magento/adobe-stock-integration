<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Magento\MediaGalleryMetadata\Model;

use Magento\MediaGalleryMetadataApi\Api\Data\MetadataInterface;
use Magento\MediaGalleryMetadataApi\Api\Data\MetadataInterfaceFactory;
use Magento\MediaGalleryMetadataApi\Api\ExtractMetadataInterface;

/**
 * Extract metadata from the asset by path
 */
class ExtractMetadata implements ExtractMetadataInterface
{
    /**
     * @var array
     */
    private $extractors;

    /**
     * @var MetadataInterfaceFactory
     */
    private $metadataFactory;

    /**
     * @param MetadataInterfaceFactory $metadataFactory
     * @param array $extractors
     */
    public function __construct(
        MetadataInterfaceFactory $metadataFactory,
        array $extractors
    ) {
        $this->extractors = $extractors;
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
        foreach ($this->extractors as $extractor) {
            $data = $extractor->execute($path);
            $title = $data->getTitle() ?? $title;
            $description = $data->getDescription() ?? $description;
            $keywords = $data->getKeywords();
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
