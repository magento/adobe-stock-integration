<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Magento\MediaGalleryMetadata\Model\Gif;

use Magento\Framework\Exception\FileSystemException;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Filesystem\DriverInterface;
use Magento\MediaGalleryMetadataApi\Model\FileInterface;
use Magento\MediaGalleryMetadataApi\Model\FileInterfaceFactory;
use Magento\MediaGalleryMetadataApi\Model\FileReaderInterface;
use Magento\MediaGalleryMetadataApi\Model\SegmentInterface;
use Magento\MediaGalleryMetadataApi\Model\SegmentInterfaceFactory;
use Magento\MediaGalleryMetadata\Model\SegmentNames;

/**
 * File segments reader
 */
class FileReader implements FileReaderInterface
{
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
        $marker = $this->read($resource, 3);
        $this->driver->fileClose($resource);

        return $marker == "GIF";
    }

    /**
     * @inheritdoc
     */
    public function execute(string $path): FileInterface
    {
        $resource = $this->driver->fileOpen($path, 'rb');

        $header = $this->read($resource, 3);

        if ($header != "GIF") {
            $this->driver->fileClose($resource);
            throw new LocalizedException(__('Not a GIF image'));
        }

        $version = $this->read($resource, 3);

        if (!in_array($version, ['87a', '89a'])) {
            $this->driver->fileClose($resource);
            throw new LocalizedException(__('Unexpected GIF version'));
        }

        $segments = [];

        //TODO: Read segments

        return $this->fileFactory->create([
            'path' => $path,
            'compressedImage' => '',
            'segments' => $segments
        ]);
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
