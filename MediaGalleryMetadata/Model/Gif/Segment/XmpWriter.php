<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Magento\MediaGalleryMetadata\Model\Gif\Segment;

use Magento\MediaGalleryMetadata\Model\AddXmpMetadata;
use Magento\MediaGalleryMetadata\Model\XmpTemplate;
use Magento\MediaGalleryMetadataApi\Api\Data\MetadataInterface;
use Magento\MediaGalleryMetadataApi\Model\FileInterface;
use Magento\MediaGalleryMetadataApi\Model\FileInterfaceFactory;
use Magento\MediaGalleryMetadataApi\Model\MetadataWriterInterface;
use Magento\MediaGalleryMetadataApi\Model\SegmentInterface;
use Magento\MediaGalleryMetadataApi\Model\SegmentInterfaceFactory;

/**
 *  XMP Writer for GIF format
 */
class XmpWriter implements MetadataWriterInterface
{
    private const XMP_SEGMENT_NAME = 'XMP DataXMP';
    private const XMP_SEGMENT_START = "XMP DataXMP";
    private const XMP_DATA_START_POSITION = 14;

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
        FileInterfaceFactory $fileFactoryInterface,
        SegmentInterfaceFactory $segmentFactoryInterface,
        AddXmpMetadata $addXmpMetadata,
        XmpTemplate $xmpTemplate
    ) {
        $this->fileFactory = $fileFactoryInterface;
        $this->segmentFactory = $segmentFactoryInterface;
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
        $gifSegments = $file->getSegments();
        $xmpGifSegments = [];
        foreach ($gifSegments as $key => $segment) {
            if ($this->isSegmentXmp($segment)) {
                $xmpGifSegments[$key] = $segment;
            }
        }

        if (empty($xmpGifSegments)) {
            return $this->fileFactory->create([
                'path' => $file->getPath(),
                'segments' => $this->insertXmpGifSegment($segments, $this->createXmpSegment($metadata))
            ]);
        }

        foreach ($xmpGifSegments as $key => $segment) {
            $gifSegments[$key] = $this->updateSegment($segment, $metadata);
        }

        return $this->fileFactory->create([
            'path' => $file->getPath(),
            'segments' => $gifSegments
        ]);
    }

    /**
     * Insert XMP segment to gif image segments (at position 1)
     *
     * @param SegmentInterface[] $segments
     * @param SegmentInterface $xmpSegment
     * @return SegmentInterface[]
     */
    private function insertXmpGifSegment(array $segments, SegmentInterface $xmpSegment): array
    {
        return array_merge(array_slice($segments, 0, 2), [$xmpSegment], array_slice($segments, 2));
    }

    /**
     * Return XMP template from string
     *
     * @param string $string
     * @param string $start
     * @param string $end
     */
    private function getXmpData(string $string, string $start, string $end): string
    {
        $string = ' ' . $string;
        $ini = strpos($string, $start);
        if ($ini == 0) {
            return '';
        }
        $ini += strlen($start);
        $len = strpos($string, $end, $ini) - $ini;
        
        return substr($string, $ini, $len);
    }
    
    /**
     * Write new segment  metadata
     *
     * @param MetadataInterface $metadata
     * @return SegmentInterface
     */
    public function createXmpSegment(MetadataInterface $metadata): SegmentInterface
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
        $xmpData = $this->getXmpData($data, 'DataXMP', "'w'?>") . "'w'?>";
        $end = substr($data, strpos($data, "xmpmeta>") + 8);

        return $this->segmentFactory->create([
            'name' => $segment->getName(),
            'data' => $start . $this->addXmpMetadata->execute($xmpData, $metadata) . $end
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
        return $segment->getName() === self::XMP_SEGMENT_NAME;
    }
}
