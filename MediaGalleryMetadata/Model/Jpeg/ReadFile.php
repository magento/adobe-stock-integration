<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Magento\MediaGalleryMetadata\Model\Jpeg;

use Magento\Framework\Exception\FileSystemException;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Filesystem\DriverInterface;
use Magento\MediaGalleryMetadataApi\Model\FileInterface;
use Magento\MediaGalleryMetadataApi\Model\FileInterfaceFactory;
use Magento\MediaGalleryMetadataApi\Model\ReadFileInterface;
use Magento\MediaGalleryMetadataApi\Model\SegmentInterface;
use Magento\MediaGalleryMetadataApi\Model\SegmentInterfaceFactory;
use Magento\MediaGalleryMetadata\Model\SegmentNames;
use Magento\Framework\Exception\ValidatorException;

/**
 * Jpeg file reader
 */
class ReadFile implements ReadFileInterface
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
     * @var SegmentInterfaceFactory
     */
    private $segmentFactory;

    /**
     * @var FileInterfaceFactory
     */
    private $fileFactory;

    /**
     * @var SegmentNames
     */
    private $segmentNames;

    /**
     * @param DriverInterface $driver
     * @param FileInterfaceFactory $fileFactory
     * @param SegmentInterfaceFactory $segmentFactory
     * @param SegmentNames $segmentNames
     */
    public function __construct(
        DriverInterface $driver,
        FileInterfaceFactory $fileFactory,
        SegmentInterfaceFactory $segmentFactory,
        SegmentNames $segmentNames
    ) {
        $this->driver = $driver;
        $this->fileFactory = $fileFactory;
        $this->segmentFactory = $segmentFactory;
        $this->segmentNames = $segmentNames;
    }

    /**
     * @inheritdoc
     */
    public function execute(string $path): FileInterface
    {
        $segments = [];
        $resource = $this->driver->fileOpen($path, 'rb');
        $marker = $this->read($resource, self::TWO_BYTES)[1];

        if ($marker != self::MARKER_IMAGE_FILE_START) {
            $this->driver->fileClose($resource);
            throw new ValidatorException(__('Not a JPEG image'));
        }

        do {
            $marker = $this->read($resource, self::TWO_BYTES)[1];
            $segments[] = $this->readSegment($resource, ord($marker));
        } while (($marker != self::MARKER_IMAGE_START) && (!$this->driver->endOfFile($resource)));

        if ($marker != self::MARKER_IMAGE_START) {
            throw new LocalizedException(__('File is corrupted'));
        }

        $segments[] = $this->segmentFactory->create([
            'name' => 'CompressedImage',
            'data' => $this->readCompressedImage($resource)
        ]);

        $this->driver->fileClose($resource);

        return $this->fileFactory->create([
            'path' => $path,
            'segments' => $segments
        ]);
    }

    /**
     * Read compressed image
     *
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
     * Read jpeg segment
     *
     * @param resource $resource
     * @param int $segmentType
     * @return SegmentInterface
     * @throws FileSystemException
     */
    private function readSegment($resource, int $segmentType): SegmentInterface
    {
        //phpcs:ignore Magento2.Functions.DiscouragedFunction
        $segmentSize = unpack('nsize', $this->read($resource, 2))['size'] - 2;
        return $this->segmentFactory->create([
            'name' => $this->segmentNames->getSegmentName($segmentType),
            'data' => $this->read($resource, $segmentSize)
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
}
