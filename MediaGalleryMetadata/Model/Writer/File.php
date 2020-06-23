<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Magento\MediaGalleryMetadata\Model\Writer;

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
     * @param FileFactory $fileFactory
     * @param SegmentFactory $segmentFactory
     * @param SegmentNames $segmentNames
     */
    public function __construct(FileFactory $fileFactory, SegmentFactory $segmentFactory, SegmentNames $segmentNames)
    {
        $this->fileFactory = $fileFactory;
        $this->segmentFactory = $segmentFactory;
        $this->segmentNames = $segmentNames;
    }

    /**
     * @param FileDataObject $file
     * @throws LocalizedException
     */
    public function execute(FileDataObject $file): void
    {
        foreach ($file->getSegments() as $segment) {
            if (strlen($segment->getData()) > 0xfffd) {
                throw new LocalizedException(__('A Header is too large to fit in the segment!'));
            }
        }

        $fileHandler = @fopen($file->getPath(), 'wb');
        if (!$fileHandler) {
            throw new LocalizedException(__('Could not open file.'));
        }

        fwrite($fileHandler, self::MARKER_IMAGE_PREFIX . self::MARKER_IMAGE_FILE_START);
        foreach ($file->getSegments() as $segment) {
            fwrite($fileHandler, self::MARKER_IMAGE_PREFIX . $this->segmentNames->getSegmentType($segment->getName()));
            fwrite($fileHandler, pack("n", strlen($segment->getData()) + 2));
            fwrite($fileHandler, $segment->getData());
        }
        fwrite($fileHandler, $file->getCompressedImage());
        fwrite($fileHandler, self::MARKER_IMAGE_PREFIX . self::MARKER_IMAGE_END);
        fclose($fileHandler);
    }
}
