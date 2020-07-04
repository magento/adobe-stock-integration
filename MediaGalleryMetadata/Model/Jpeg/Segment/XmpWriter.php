<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Magento\MediaGalleryMetadata\Model\Jpeg\Segment;

use Magento\MediaGalleryMetadata\Model\AddXmpMetadata;
use Magento\MediaGalleryMetadata\Model\XmpTemplate;
use Magento\MediaGalleryMetadataApi\Api\Data\MetadataInterface;
use Magento\MediaGalleryMetadataApi\Model\FileInterface;
use Magento\MediaGalleryMetadataApi\Model\FileInterfaceFactory;
use Magento\MediaGalleryMetadataApi\Model\MetadataWriterInterface;
use Magento\MediaGalleryMetadataApi\Model\SegmentInterface;
use Magento\MediaGalleryMetadataApi\Model\SegmentInterfaceFactory;

/**
 * Jpeg XMP Writer
 */
class XmpWriter implements MetadataWriterInterface
{
    private const XMP_SEGMENT_NAME = 'APP1';
    private const XMP_SEGMENT_START = "http://ns.adobe.com/xap/1.0/\x00";
    private const XMP_DATA_START_POSITION = 29;

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
     * @var XmpTemplate
     */
    private $xmpTemplate;

    /**
     * @param FileInterfaceFactory $fileFactory
     * @param SegmentInterfaceFactory $segmentFactory
     * @param AddXmpMetadata $addXmpMetadata
     * @param XmpTemplate $xmpTemplate
     */
    public function __construct(
        FileInterfaceFactory $fileFactory,
        SegmentInterfaceFactory $segmentFactory,
        AddXmpMetadata $addXmpMetadata,
        XmpTemplate $xmpTemplate
    ) {
        $this->fileFactory = $fileFactory;
        $this->segmentFactory = $segmentFactory;
        $this->addXmpMetadata = $addXmpMetadata;
        $this->xmpTemplate = $xmpTemplate;
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
        $xmpSegment = [];
        foreach ($segments as $key => $segment) {
            if ($this->isSegmentXmp($segment)) {
                $xmpSegment[$key] = $segment;
            }
        }
        if (!empty($xmpSegment)) {
            foreach ($xmpSegment as $key => $segment) {
                $segments[$key] = $this->updateSegment($segment, $metadata);
            }
        } else {
            $segments[] = $this->writeSegment($metadata);
        }

        return $this->fileFactory->create([
            'path' => $file->getPath(),
            'segments' => $segments
        ]);
    }

    /**
     * Write new segment  metadata
     *
     * @param MetadataInterface $metadata
     * @return SegmentInterface
     */
    public function writeSegment(MetadataInterface $metadata): SegmentInterface
    {
        $xmpData = $this->xmpTemplate->get();
        return $this->segmentFactory->create([
            'name' => self::XMP_SEGMENT_NAME,
            'data' => self::XMP_SEGMENT_START . $this->addXmpMetadata->execute($xmpData, $metadata)
        ]);
    }
    
    /**
     * Add metadata to the segment
     *
     * @param SegmentInterface $segment
     * @param MetadataInterface $metadata
     * @return SegmentInterface
     */
    public function updateSegment(SegmentInterface $segment, MetadataInterface $metadata): SegmentInterface
    {
        $data = $segment->getData();
        $start = substr($data, 0, self::XMP_DATA_START_POSITION);
        $xmpData = substr($data, self::XMP_DATA_START_POSITION);
        return $this->segmentFactory->create([
            'name' => $segment->getName(),
            'data' => $start . $this->addXmpMetadata->execute($xmpData, $metadata)
        ]);
    }

    /**
     * Check if segment contains XMP data
     *
     * @param SegmentInterface $segment
     * @return bool
     */
    private function isSegmentXmp(SegmentInterface $segment): bool
    {
        return $segment->getName() === self::XMP_SEGMENT_NAME
            && strncmp($segment->getData(), self::XMP_SEGMENT_START, self::XMP_DATA_START_POSITION) == 0;
    }
}
