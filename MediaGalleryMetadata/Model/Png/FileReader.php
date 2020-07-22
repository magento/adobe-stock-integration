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
     * @param DriverInterface $driver
     * @param FileInterfaceFactory $fileFactory
     * @param SegmentInterfaceFactory $segmentFactory
     */
    public function __construct(
        DriverInterface $driver,
        FileInterfaceFactory $fileFactory,
        SegmentInterfaceFactory $segmentFactory
    ) {
        $this->driver = $driver;
        $this->fileFactory = $fileFactory;
        $this->segmentFactory = $segmentFactory;
    }

    /**
     * Is the current file reader applicable to a given path
     *
     * @param string $path
     * @return bool
     */
    private function isApplicable(string $path): bool
    {
        $resource = $this->driver->fileOpen($path, 'rb');
        $marker = $this->readHeader($resource);
        $this->driver->fileClose($resource);

        return $marker == self::PNG_FILE_START;
    }

    /**
     * @inheritdoc
     */
    public function execute(string $path): ?FileInterface
    {
        $segments = [];
                
        if (!$this->isApplicable($path)) {
            return null;
        }
        $resource = $this->driver->fileOpen($path, 'rb');

        $header = $this->readHeader($resource);

        if ($header != self::PNG_FILE_START) {
            $this->driver->fileClose($resource);
            throw new LocalizedException(__('Not a PNG image'));
        }

        do {
            $header = $this->readHeader($resource);
            //phpcs:ignore Magento2.Functions.DiscouragedFunction
            $segmentHeader = unpack('Nsize/a4type', $header);
            $data = $this->read($resource, $segmentHeader['size']);
            $segments[] = $this->segmentFactory->create([
                'name' => $segmentHeader['type'],
                'data' => $data
            ]);
            $cyclicRedundancyCheck = $this->read($resource, 4);

            if (pack('N', crc32($segmentHeader['type'] . $data)) != $cyclicRedundancyCheck) {
                throw new LocalizedException(__('The image is corrupted'));
            }
        } while ($header
            && $segmentHeader['type'] != self::PNG_MARKER_IMAGE_END
            && !$this->driver->endOfFile($resource)
        );

        $this->driver->fileClose($resource);

        return $this->fileFactory->create([
            'path' => $path,
            'segments' => $segments
        ]);
    }

    /**
     * Read 8 bytes
     *
     * @param resource $resource
     * @return string
     * @throws FileSystemException
     */
    private function readHeader($resource): string
    {
        return $this->read($resource, 8);
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
