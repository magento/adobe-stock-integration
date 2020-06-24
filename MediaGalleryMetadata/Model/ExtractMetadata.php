<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Magento\MediaGalleryMetadata\Model;

use Magento\MediaGalleryMetadataApi\Model\ReaderPool;
use Magento\MediaGalleryMetadataApi\Api\Data\MetadataInterface;
use Magento\MediaGalleryMetadataApi\Api\Data\MetadataInterfaceFactory;
use Magento\MediaGalleryMetadataApi\Api\ExtractMetadataInterface;

/**
 * Extract metadata from the asset
 */
class ExtractMetadata implements ExtractMetadataInterface
{
    /**
     * @var ReaderPool
     */
    private $readerPool;

    /**
     * @var MetadataInterfaceFactory
     */
    private $metadataFactory;

    /**
     * @param ReaderPool $readerPool
     * @param MetadataInterfaceFactory $metadataFactory
     */
    public function __construct(ReaderPool $readerPool, MetadataInterfaceFactory $metadataFactory)
    {
        $this->readerPool = $readerPool;
        $this->metadataFactory = $metadataFactory;
    }

    /**
     * @param string $path
     * @return MetadataInterface
     */
    public function execute(string $path): MetadataInterface
    {
        $title = '';
        $description = '';
        $keywords = [];
        foreach ($this->readerPool->get() as $reader) {
            $data = $reader->execute($path);
            $title = $data->getTitle() ?? $title;
            $description = $data->getDescription() ?? $description;
            $keywords = array_unique(array_merge($keywords, $data->getKeywords()));
            if (!empty($data) && !empty($description) && !empty($keywords)) {
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
