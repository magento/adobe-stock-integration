<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Magento\MediaGalleryMetadata\Model\Gif\Segment;

use Magento\Framework\Exception\LocalizedException;
use Magento\MediaGalleryMetadata\Model\GetXmpMetadata;
use Magento\MediaGalleryMetadataApi\Api\Data\MetadataInterface;
use Magento\MediaGalleryMetadataApi\Api\Data\MetadataInterfaceFactory;
use Magento\MediaGalleryMetadataApi\Model\FileInterface;
use Magento\MediaGalleryMetadataApi\Model\MetadataReaderInterface;
use Magento\MediaGalleryMetadataApi\Model\SegmentInterface;

/**
 * XMP Reader
 */
class XmpReader implements MetadataReaderInterface
{
    private const XMP_SEGMENT_NAME = 'XMP DataXMP';

    /**
     * @var MetadataInterfaceFactory
     */
    private $metadataFactory;

    /**
     * @var GetXmpMetadata
     */
    private $getXmpMetadata;

    /**
     * @param MetadataInterfaceFactory $metadataFactory
     */
    public function __construct(MetadataInterfaceFactory $metadataFactory, GetXmpMetadata $getXmpMetadata)
    {
        $this->metadataFactory = $metadataFactory;
        $this->getXmpMetadata = $getXmpMetadata;
    }

    /**
     * @inheritdoc
     */
    public function execute(FileInterface $file): MetadataInterface
    {
        foreach ($file->getSegments() as $segment) {
            if ($this->isXmpSegment($segment)) {
                return $this->getXmpMetadata->execute($this->getXmpData($segment));
            }
        }
        return $this->metadataFactory->create([
            'title' => '',
            'description' => '',
            'keywords' => []
        ]);
    }

    /**
     * Does segment contain XMP data
     *
     * @param SegmentInterface $segment
     * @return bool
     */
    private function isXmpSegment(SegmentInterface $segment): bool
    {
        return $segment->getName() === self::XMP_SEGMENT_NAME;
    }

    /**
     * Get XMP xml
     *
     * @param SegmentInterface $segment
     * @return string
     */
    private function getXmpData(SegmentInterface $segment): string
    {
        $xmp = substr($segment->getData(), 13);

        if (substr($xmp, -257, 3) !== "\x01\xFF\xFE" || substr($xmp, -4) !== "\x03\x02\x01\x00") {
            throw new LocalizedException(__('XMP data is corrupted'));
        }

        $xmp = substr($xmp, 0, -257);
        return $xmp;
    }
}
