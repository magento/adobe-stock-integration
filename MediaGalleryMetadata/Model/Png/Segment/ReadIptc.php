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
    private const IPTC_SEGMENT_START = 'iptc';
    private const IPTC_DATA_START_POSITION = 17;

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
                return $this->getIptcData($segment);
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
     * @param FileInterface $file
     */
    private function getIptcData(SegmentInterface $segment): MetadataInterface
    {
        $data = trim(preg_replace('/\s+/', '', substr(gzinflate(substr($segment->getData(), 25)), 5)));
        $binData = hex2bin($data);

        $descriptionMarker = pack("C", 2) . 'x' . pack("C", 0);
        $description = substr(
            $binData,
            strpos($binData, $descriptionMarker) + 4,
            ord(substr($binData, strpos($binData, $descriptionMarker) + 3, 1))
        );

        $titleMarker =  pack("C", 2) . 'i' . pack("C", 0);
        $title = substr(
            $binData,
            strpos($binData, $titleMarker) + 4,
            ord(substr($binData, strpos($binData, $titleMarker) + 3, 1))
        );

        $keywordsMarker = pack("C", 2) . pack("C", 25) . pack("C", 0);
        $keywords = substr(
            $binData,
            strpos($binData, $keywordsMarker) + 4,
            ord(substr($binData, strpos($binData, $keywordsMarker) + 3, 1))
        );


        return $this->metadataFactory->create([
            'title' => $title,
            'description' => $description,
            'keywords' => explode(',', $keywords)
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
            && strncmp(
                substr($segment->getData(), self::IPTC_DATA_START_POSITION, 4),
                self::IPTC_SEGMENT_START,
                self::IPTC_DATA_START_POSITION
            ) == 0;
    }
}
