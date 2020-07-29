<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Magento\MediaGalleryMetadata\Model\Png\Segment;

use Magento\MediaGalleryMetadata\Model\GetIptcMetadata;
use Magento\MediaGalleryMetadataApi\Api\Data\MetadataInterface;
use Magento\MediaGalleryMetadataApi\Api\Data\MetadataInterfaceFactory;
use Magento\MediaGalleryMetadataApi\Model\FileInterface;
use Magento\MediaGalleryMetadataApi\Model\ReadMetadataInterface;
use Magento\MediaGalleryMetadataApi\Model\SegmentInterface;
use Magento\Framework\Filesystem\DriverInterface;

/**
 * IPTC Reader to read IPTC data for png image
 */
class ReadIptc implements ReadMetadataInterface
{
    private const IPTC_SEGMENT_NAME = 'zTXt';
    private const IPTC_SEGMENT_START = 'Raw profile type iptc';
    private const IPTC_DATA_START_POSITION = 0;

    /**
     * @var MetadataInterfaceFactory
     */
    private $metadataFactory;

    /**
     * @var DriverInterface
     */
    private $driver;

    /**
     * @param DriverInterface $driver
     * @param MetadataInterfaceFactory $metadataFactory
     */
    public function __construct(
        DriverInterface $driver,
        MetadataInterfaceFactory $metadataFactory
    ) {
        $this->driver = $driver;
        $this->metadataFactory = $metadataFactory;
    }

    /**
     * @inheritdoc
     */
    public function execute(FileInterface $file): MetadataInterface
    {
        foreach ($file->getSegments() as $segment) {
            if ($this->isIptcSegment($segment)) {
                return $this->getIptcData($segment, $file);
            }
        }
        
        return $this->metadataFactory->create([
            'title' => null,
            'description' => null,
            'keywords' => null
        ]);
    }

    /**
     * Read iptc data from zTXt segment
     *
     * @param SegmentInterface $segment
     * @param FileInterface $file
     */
    private function getIptcData(SegmentInterface $segment, FileInterface $file): MetadataInterface
    {
        $resource = $this->driver->fileOpen($file->getPath(), 'rb');
        $data = $this->read($resource, 8);
        return $this->metadataFactory->create([
            'title' => '',
            'description' => '',
            'keywords' => []
        ]);
    }

    /**
     * Read wrapper
     *
     * @param resource $resource
     * @param int $length
     * @return string
     * @throws FileSystemException
     */
    private function read($resource, int $length): string
    {
        $data = '';

        while (!$this->driver->endOfFile($resource) && strlen($data) < $length) {
            $data .= $this->driver->fileRead($resource, $length - strlen($data));
        }

        return $data;
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
            && strncmp($segment->getData(), self::IPTC_SEGMENT_START, self::IPTC_DATA_START_POSITION) == 0;
    }
}
