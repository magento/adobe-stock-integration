<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Magento\MediaGalleryMetadata\Model\Png;

use Magento\Framework\Exception\FileSystemException;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Filesystem\DriverInterface;
use Magento\MediaGalleryMetadataApi\Model\FileInterface;
use Magento\MediaGalleryMetadataApi\Model\FileInterfaceFactory;
use Magento\MediaGalleryMetadataApi\Model\FileReaderInterface;
use Magento\MediaGalleryMetadataApi\Model\SegmentInterfaceFactory;
use Magento\MediaGalleryMetadata\Model\SegmentNames;

/**
 * File segments reader
 */
class FileReader implements FileReaderInterface
{
    private const PNG_FILE_START = "\x89PNG\x0d\x0a\x1a\x0a";
    private const PNG_MARKER_IMAGE_END = 'IEND';
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
    public function isApplicable(string $path): bool
    {
        $resource = $this->driver->fileOpen($path, 'rb');
        $marker = $this->readHeader($resource);
        $this->driver->fileClose($resource);

        return $marker == self::PNG_FILE_START;
    }

    /**
     * @inheritdoc
     */
    public function execute(string $path): FileInterface
    {
        $resource = $this->driver->fileOpen($path, 'rb');

        $header = $this->readHeader($resource);

        if ($header != self::PNG_FILE_START) {
            $this->driver->fileClose($resource);
            throw new LocalizedException(__('Not a PNG image'));
        }

        $segments = [];

        do {
            $header = $this->readHeader($resource);
            $segmentHeader = unpack('Nsize/a4type', $header);
            $segments[] = $this->segmentFactory->create([
                'name' => $segmentHeader['type'],
                'dataStart' => $this->driver->fileTell($resource),
                'data' => $this->read($resource, $segmentHeader['size'])
            ]);
            $this->driver->fileSeek($resource, 4, SEEK_CUR);
        } while (
            $header
            && $segmentHeader['type'] != self::PNG_MARKER_IMAGE_END
            && !$this->driver->endOfFile($resource)
        );

        $this->driver->fileClose($resource);

        return $this->fileFactory->create([
            'path' => $path,
            'compressedImage' => '',
            'segments' => $segments
        ]);
    }

    /**
     * @param resource $resource
     * @return string
     * @throws FileSystemException
     */
    private function readHeader($resource): string
    {
        return $this->read($resource, 8);
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
