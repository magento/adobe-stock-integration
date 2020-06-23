<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Magento\MediaGalleryMetadata\Model\Reader;

use Magento\Framework\Exception\LocalizedException;
use Magento\MediaGalleryMetadata\Model\File as FileDataObject;
use Magento\MediaGalleryMetadata\Model\FileFactory;
use Magento\MediaGalleryMetadata\Model\SegmentFactory;
use Magento\MediaGalleryMetadata\Model\SegmentNames;

/**
 * File segments reader
 */
class File
{
    private const MARKER_IMAGE_FILE_START = "\xD8";
    private const MARKER_IMAGE_PREFIX = "\xFF";
    private const MARKER_IMAGE_END = "\xD9";
    private const MARKER_IMAGE_START = "\xDA";

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

    public function __construct(FileFactory $fileFactory, SegmentFactory $segmentFactory, SegmentNames $segmentNames)
    {
        $this->fileFactory = $fileFactory;
        $this->segmentFactory = $segmentFactory;
        $this->segmentNames = $segmentNames;
    }

    /**
     * @param string $path
     * @return FileDataObject
     * @throws LocalizedException
     */
    public function execute(string $path): FileDataObject
    {
        $fileHandle = @fopen($path, 'rb');

        if (!$fileHandle) {
            throw new LocalizedException(__('Cannot open file'));
        }

        $data = $this->read($fileHandle, 2);

        if ($data != self::MARKER_IMAGE_PREFIX . self::MARKER_IMAGE_FILE_START) {
            fclose($fileHandle);
            throw new LocalizedException(__('Not an image'));
        }

        $data = $this->read($fileHandle, 2);

        if ($data[0] != self::MARKER_IMAGE_PREFIX) {
            fclose($fileHandle);
            throw new LocalizedException(__('File is corrupted'));
        }

        $segments = [];

        while (($data[1] != self::MARKER_IMAGE_END) && (!feof($fileHandle))) {
            if ((ord($data[1]) < 0xD0) || (ord($data[1]) > 0xD7)) {
                $sizestr = $this->read($fileHandle, 2);

                $decodedsize = unpack("nsize", $sizestr);

                $segmentDataStartPosition = ftell($fileHandle);

                $segmentData = $this->read($fileHandle, $decodedsize['size'] - 2);

                $segments[] = $this->segmentFactory->create([
                    'name' => $this->segmentNames->getSegmentName(ord($data[1])),
                    'dataStart' => $segmentDataStartPosition,
                    'data' => $segmentData,
                ]);
            }

            if ($data[1] == self::MARKER_IMAGE_START) {
                $compressedImage = '';
                do {
                    $compressedImage .= $this->read($fileHandle, 1048576);
                } while (!feof($fileHandle));

                $endOfImageMarkerPosition = strpos($compressedImage, self::MARKER_IMAGE_PREFIX . self::MARKER_IMAGE_END);
                $compressedImage = substr($compressedImage, 0, $endOfImageMarkerPosition);

                fclose($fileHandle);

                return $this->fileFactory->create([
                    'path' => $path,
                    'compressedImage' => $compressedImage,
                    'segments' => $segments,
                ]);
            }

            $data = $this->read($fileHandle, 2);

            if ($data[0] != self::MARKER_IMAGE_PREFIX) {
                fclose($fileHandle);
                throw new LocalizedException(__('File is corrupted'));
            }
        }

        fclose($fileHandle);

        throw new LocalizedException(__('File is corrupted'));
    }

    private function read($fileHandle, int $length): string
    {
        $data = '';

        while (!feof($fileHandle) && strlen($data) < $length) {
            $data .= fread($fileHandle, $length - strlen($data));
        }

        return $data;
    }
}
