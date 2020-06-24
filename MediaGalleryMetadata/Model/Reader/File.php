<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Magento\MediaGalleryMetadata\Model\Reader;

use Magento\Framework\Exception\FileSystemException;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Filesystem\DriverInterface;
use Magento\MediaGalleryMetadata\Model\File as FileDataObject;
use Magento\MediaGalleryMetadata\Model\FileFactory;
use Magento\MediaGalleryMetadata\Model\Segment;
use Magento\MediaGalleryMetadata\Model\SegmentFactory;
use Magento\MediaGalleryMetadata\Model\SegmentNames;

/**
 * File segments reader
 */
class File
{
    private const MARKER_IMAGE_FILE_START = "\xD8";
    private const MARKER_PREFIX = "\xFF";
    private const MARKER_IMAGE_END = "\xD9";
    private const MARKER_IMAGE_START = "\xDA";

    private const TWO_BYTES = 2;
    private const ONE_MEGABYTE = 1048576;

    /**
     * @var DriverInterface
     */
    private $driver;

    /**
     * @var SegmentFactory
     */
    private $segmentFactory;

    /**
     * @var FileFactory
     */
    private $fileFactory;

    /**
     * @var SegmentNames
     */
    private $segmentNames;

    /**
     * @param DriverInterface $driver
     * @param FileFactory $fileFactory
     * @param SegmentFactory $segmentFactory
     * @param SegmentNames $segmentNames
     */
    public function __construct(
        DriverInterface $driver,
        FileFactory $fileFactory,
        SegmentFactory $segmentFactory,
        SegmentNames $segmentNames
    ) {
        $this->driver = $driver;
        $this->fileFactory = $fileFactory;
        $this->segmentFactory = $segmentFactory;
        $this->segmentNames = $segmentNames;
    }

    /**
     * @param string $path
     * @return FileDataObject
     * @throws FileSystemException
     * @throws LocalizedException
     */
    public function execute(string $path): FileDataObject
    {
        $resource = $this->driver->fileOpen($path, 'rb');

        $marker = $this->readMarker($resource);

        if ($marker != self::MARKER_IMAGE_FILE_START) {
            $this->driver->fileClose($resource);
            throw new LocalizedException(__('Not an image'));
        }

        $segments = [];

        do {
            $marker = $this->readMarker($resource);
            $segments[] = $this->readSegment($resource, ord($marker));
        } while (($marker != self::MARKER_IMAGE_START) && (!$this->driver->endOfFile($resource)));

        if ($marker != self::MARKER_IMAGE_START) {
            throw new LocalizedException(__('File is corrupted'));
        }

        $compressedImage = $this->readCompressedImage($resource);

        $this->driver->fileClose($resource);

        return $this->fileFactory->create([
            'path' => $path,
            'compressedImage' => $compressedImage,
            'segments' => $segments
        ]);
    }

    /**
     * @param resource $resource
     * @return string
     * @throws FileSystemException
     */
    private function readCompressedImage($resource): string
    {
        $compressedImage = '';
        do {
            $compressedImage .= $this->read($resource, self::ONE_MEGABYTE);
        } while (!$this->driver->endOfFile($resource));

        $endOfImageMarkerPosition = strpos($compressedImage, self::MARKER_PREFIX . self::MARKER_IMAGE_END);
        $compressedImage = substr($compressedImage, 0, $endOfImageMarkerPosition);

        return $compressedImage;
    }

    /**
     * @param resource $resource
     * @param int $segmentType
     * @return Segment
     * @throws FileSystemException
     */
    private function readSegment($resource, int $segmentType): Segment
    {
        $segmentSize = unpack('nsize', $this->read($resource, 2))['size'] - 2;
        return $this->segmentFactory->create([
            'name' => $this->segmentNames->getSegmentName($segmentType),
            'dataStart' => $this->driver->fileTell($resource),
            'data' => $this->read($resource, $segmentSize)
        ]);
    }

    /**
     * @param resource $resource
     * @return string
     * @throws FileSystemException
     */
    private function readMarker($resource): string
    {
        $data = $this->read($resource, self::TWO_BYTES);

        if ($data[0] != self::MARKER_PREFIX) {
            $this->driver->fileClose($resource);
            throw new LocalizedException(__('File is corrupted'));
        }

        return $data[1];
    }

    /**
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
}
