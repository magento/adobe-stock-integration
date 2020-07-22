<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Magento\MediaGalleryMetadata\Model\Png\Segment;

use Magento\MediaGalleryMetadata\Model\AddXmpMetadata;
use Magento\MediaGalleryMetadataApi\Api\Data\MetadataInterface;
use Magento\MediaGalleryMetadataApi\Model\FileInterface;
use Magento\MediaGalleryMetadataApi\Model\FileInterfaceFactory;
use Magento\MediaGalleryMetadataApi\Model\MetadataWriterInterface;
use Magento\MediaGalleryMetadataApi\Model\SegmentInterface;
use Magento\MediaGalleryMetadataApi\Model\SegmentInterfaceFactory;

/**
 * XMP Reader
 */
class XmpWriter implements MetadataWriterInterface
{
    private const XMP_SEGMENT_NAME = 'iTXt';
    private const XMP_SEGMENT_START = "XML:com.adobe.xmp\x00";

    /**
     * @var SegmentInterfaceFactory
     */
    private $segmentFactory;

    /**
     * @var FileInterfaceFactory
     */
    private $fileFactory;

    /**
     * @var AddXmpMetadata
     */
    private $addXmpMetadata;

    /**
     * @param FileInterfaceFactory $fileFactory
     * @param SegmentInterfaceFactory $segmentFactory
     * @param AddXmpMetadata $addXmpMetadata
     */
    public function __construct(
        FileInterfaceFactory $fileFactory,
        SegmentInterfaceFactory $segmentFactory,
        AddXmpMetadata $addXmpMetadata
    ) {
        $this->fileFactory = $fileFactory;
        $this->segmentFactory = $segmentFactory;
        $this->addXmpMetadata = $addXmpMetadata;
    }

    /**
     * Add metadata to the file
     *
     * @param FileInterface $file
     * @param MetadataInterface $metadata
     * @return FileInterface
     */
    public function execute(FileInterface $file, MetadataInterface $metadata): FileInterface
    {
        $segments = $file->getSegments();
        foreach ($segments as $key => $segment) {
            if ($this->isXmpSegment($segment)) {
                $segments[$key] = $this->updateSegment($segment, $metadata);
            }
        }
        return $this->fileFactory->create([
            'path' => $file->getPath(),
            'segments' => $segments
        ]);
    }

    /**
     * Add metadata to the segment
     *
     * @param SegmentInterface $segment
     * @param MetadataInterface $metadata
     * @return SegmentInterface
     */
    private function updateSegment(SegmentInterface $segment, MetadataInterface $metadata): SegmentInterface
    {
        return $this->segmentFactory->create([
            'name' => $segment->getName(),
            'data' => self::XMP_SEGMENT_START . $this->addXmpMetadata->execute($this->getXmpData($segment), $metadata)
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
        return $segment->getName() === self::XMP_SEGMENT_NAME
            && strpos($segment->getData(), '<x:xmpmeta') !== -1;
    }

    /**
     * Get XMP xml
     *
     * @param SegmentInterface $segment
     * @return string
     */
    private function getXmpData(SegmentInterface $segment): string
    {
        return substr($segment->getData(), strpos($segment->getData(), '<x:xmpmeta'));
    }
}
