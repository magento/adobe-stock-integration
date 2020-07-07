<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Magento\MediaGalleryMetadata\Model\Jpeg\Segment;

use Magento\MediaGalleryMetadata\Model\AddIptcMetadata;
use Magento\MediaGalleryMetadataApi\Api\Data\MetadataInterface;
use Magento\MediaGalleryMetadataApi\Model\FileInterface;
use Magento\MediaGalleryMetadataApi\Model\FileInterfaceFactory;
use Magento\MediaGalleryMetadataApi\Model\MetadataWriterInterface;
use Magento\MediaGalleryMetadataApi\Model\SegmentInterface;
use Magento\MediaGalleryMetadataApi\Model\SegmentInterfaceFactory;

/**
 * Jpeg IPTC Writer
 */
class IptcWriter implements MetadataWriterInterface
{
    private const IPTC_SEGMENT_NAME = 'APP13';
    private const IPTC_SEGMENT_START = 'Photoshop 3.0\0x00';

    /**
     * @var SegmentInterfaceFactory
     */
    private $segmentFactory;

    /**
     * @var FileInterfaceFactory
     */
    private $fileFactory;

    /**
     * @var AddIPtcMetadata
     */
    private $addIptcMetadata;

    /**
     * @param FileInterfaceFactory $fileFactory
     * @param SegmentInterfaceFactory $segmentFactory
     * @param AddIptcMetadata $addIptcMetadata
     */
    public function __construct(
        FileInterfaceFactory $fileFactory,
        SegmentInterfaceFactory $segmentFactory,
        AddIptcMetadata $addIptcMetadata
    ) {
        $this->fileFactory = $fileFactory;
        $this->segmentFactory = $segmentFactory;
        $this->addIptcMetadata = $addIptcMetadata;
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
        $iptcSegments = [];
        foreach ($segments as $key => $segment) {
            if ($this->isIptcSegment($segment)) {
                $iptcSegments[$key] = $segment;
            }
        }

        if (empty($iptcSegments)) {
            return  $this->addIptcMetadata->execute($file, $metadata, null);
        }

        foreach ($iptcSegments as $segment) {
            $file =  $this->addIptcMetadata->execute($file, $metadata, $segment);
        }
        return $file ?? $this->fileFactory->create([
            'path' => $file->getPath(),
            'segments' => $segments
        ]);
    }

    /**
     * Check if segment contains IPTC data
     *
     * @param SegmentInterface $segment
     * @return bool
     */
    private function isIptcSegment(SegmentInterface $segment): bool
    {
        return $segment->getName() === self::IPTC_SEGMENT_NAME
            && strncmp($segment->getData(), self::IPTC_SEGMENT_START, self::IPTC_DATA_START_POSITION) == 0;
    }
}
