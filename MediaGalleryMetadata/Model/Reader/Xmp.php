<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Magento\MediaGalleryMetadata\Model\Reader;

use Magento\MediaGalleryMetadataApi\Api\Data\MetadataInterface;
use Magento\MediaGalleryMetadataApi\Model\FileInterface;
use Magento\MediaGalleryMetadataApi\Model\MetadataReaderInterface;
use Magento\MediaGalleryMetadata\Model\Reader\File as FileReader;

/**
 * XMP Reader
 */
class Xmp implements MetadataReaderInterface
{
    /**
     * @var FileReader
     */
    private $reader;

    /**
     * @var MetadataInterfaceFactory
     */
    private $metadataFactory;

    /**
     * @param MetadataInterfaceFactory $metadataFactory
     */
    public function __construct(MetadataInterfaceFactory $metadataFactory, FileReader $reader)
    {
        $this->metadataFactory = $metadataFactory;
        $this->reader = $reader;
    }

    /**
     * @inheritdoc
     */
    public function execute(string $path): MetadataInterface
    {
        $file = $this->reader->execute($path);
        return $this->extractData($file);
    }

    /**
     * @param FileInterface $file
     * @return MetadataInterface
     */
    public function extractData(FileInterface $file): MetadataInterface
    {
        foreach ($file->getSegments() as $segment) {
            if ($segment->getName() === 'APP1' && strpos($segment->getData(), '<x:xmpmeta') !== false) {
                //TODO: Parse segment
            }
        }
        return $this->metadataFactory->create([
            'title' => '',
            'description' => '',
            'keywords' => []
        ]);
    }
}
