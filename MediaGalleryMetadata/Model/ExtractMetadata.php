<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Magento\MediaGalleryMetadata\Model;

use Magento\MediaGalleryMetadataApi\Model\ExtractMetadataPool;
use Magento\MediaGalleryMetadataApi\Api\Data\MetadataInterface;
use Magento\MediaGalleryMetadataApi\Api\Data\MetadataInterfaceFactory;
use Magento\MediaGalleryMetadataApi\Api\ExtractMetadataInterface;

/**
 * Extract metadata from the asset by path
 */
class ExtractMetadata implements ExtractMetadataInterface
{
    /**
     * @var ExtractMetadataPool
     */
    private $extractorsPool;

    /**
     * @var MetadataInterfaceFactory
     */
    private $metadataFactory;

    /**
     * @param ExtractMetadataPool $extractorsPool
     * @param MetadataInterfaceFactory $metadataFactory
     */
    public function __construct(ExtractMetadataPool $extractorsPool, MetadataInterfaceFactory $metadataFactory)
    {
        $this->extractorsPool = $extractorsPool;
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
        foreach ($this->extractorsPool->get() as $extractor) {
            $data = $extractor->execute($path);
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
