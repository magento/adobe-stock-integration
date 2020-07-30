<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Magento\MediaGalleryMetadata\Model\Png\Segment;

use Magento\MediaGalleryMetadataApi\Api\Data\MetadataInterface;
use Magento\MediaGalleryMetadataApi\Model\FileInterface;
use Magento\MediaGalleryMetadataApi\Model\FileInterfaceFactory;
use Magento\MediaGalleryMetadataApi\Model\SegmentInterface;
use Magento\MediaGalleryMetadataApi\Model\WriteMetadataInterface;

/**
 * IPTC Writer to write IPTC data for png image
 */
class WriteIPtc implements WriteMetadataInterface
{
    private const IPTC_SEGMENT_NAME = 'zTXt';
    private const IPTC_SEGMENT_START = 'iptc';
    private const IPTC_DATA_START_POSITION = 17;

    /**
     * @var SegmentInterfaceFactory
     */
    private $segmentFactory;

    /**
     * @var FileInterfaceFactory
     */
    private $fileFactory;

    /**
     * @param FileInterfaceFactory $fileFactory
     * @param SegmentInterfaceFactory $segmentFactory
     */
    public function __construct(
        FileInterfaceFactory $fileFactory,
        SegmentInterfaceFactory $segmentFactory
    ) {
        $this->fileFactory = $fileFactory;
        $this->segmentFactory = $segmentFactory;
    }

    /**
     * @inheritdoc
     */
    public function execute(FileInterface $file): MetadataInterface
    {
        $segments = $file->getSegments();
        $pngIptcSegments = [];
        foreach ($segments as $key => $segment) {
            if ($this->isIptcSegment($segment)) {
                $pngIptcSegments[$key] = $segment;
            }
        }

        if (empty($pngIptcSegments)) {
            return $this->fileFactory->create([
                'path' => $file->getPath(),
                'segments' => $segments[] =  $this->createPngIptcSegment($metadata)
            ]);
        }

        foreach ($pngXmpSegments as $key => $segment) {
            $segments[$key] = $this->updateIptcSegment($segment, $metadata);
        }

        return $this->fileFactory->create([
            'path' => $file->getPath(),
            'segments' => $segments
        ]);
    }

    /**
     * Read iptc data from zTXt segment
     *
     * @param SegmentInterface $segment
     * @param MetadataInterface $metadata
     */
    private function updateIptcSegment(SegmentInterface $segment, MetadataInterface $metadata): SegmentInterface
    {
        $description = null;
        $title = null;
        $keywords = null;
        
        $iptSegmentStartPosition = strpos($segment->getData(), pack("C", 0) . pack("C", 0) . 'x');
        //phpcs:ignore Magento2.Functions.DiscouragedFunction
        $uncompressedData = gzuncompress(substr($segment->getData(), $iptSegmentStartPosition + 2));
        
        $data = explode(PHP_EOL, trim($uncompressedData));
        //remove header and size from hex string
        $iptcData = implode(array_slice($data, 2));
        $binData = hex2bin($iptcData);

        $descriptionMarker = pack("C", 2) . 'x' . pack("C", 0);
        $descriptionStartPosition = strpos($binData, $descriptionMarker) + 4;
        

        $titleMarker =  pack("C", 2) . 'i' . pack("C", 0);
        $titleStartPosition = strpos($binData, $titleMarker);

        $keywordsMarker = pack("C", 2) . pack("C", 25) . pack("C", 0);
        $keywordsStartPosition = strpos($binData, $keywordsMarker);

        return $this->segmentFactory->create([
            'name' => $segment->getName(),
            'data' => $segmentDataCompressed
        ]);
    }

    /**
     * Does segment contain IPTC data
     *
     * @param SegmentInterface $segment
     * @return bool
     */
    private function isIptcSegment(SegmentInterface $segment): bool
    {
        return $segment->getName() === self::IPTC_SEGMENT_NAME
            && strncmp(
                substr($segment->getData(), self::IPTC_DATA_START_POSITION, 4),
                self::IPTC_SEGMENT_START,
                self::IPTC_DATA_START_POSITION
            ) == 0;
    }
}
