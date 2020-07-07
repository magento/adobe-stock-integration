<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Magento\MediaGalleryMetadata\Model;

use Magento\MediaGalleryMetadataApi\Api\Data\MetadataInterface;
use Magento\MediaGalleryMetadataApi\Model\FileInterface;
use Magento\MediaGalleryMetadataApi\Model\SegmentInterface;
use Magento\MediaGalleryMetadata\Model\Jpeg\FileReader;
use Magento\Framework\Filesystem\DriverInterface;

/**
 * Add metadata to the IPTC data
 */
class AddIptcMetadata
{
    private const IPTC_TITLE_SEGMENT = '2#005';
    private const IPTC_DESCRIPTION_SEGMENT = '2#120';
    private const IPTC_KEYWORDS_SEGMENT = '2#025';

    /**
     * @var DriverInterface
     */
    private $driver;

    /**
     * @var FileReader
     */
    private $fileReader;

    /**
     * @param DriverInterface $driver
     * @param FileReader $fileReader
     */
    public function __construct(
        DriverInterface $driver,
        FileReader $fileReader
    ) {
        $this->driver = $driver;
        $this->fileReader = $fileReader;
    }
    
    /**
     * Write metadata
     *
     * @param FileInterface $file
     * @param MetadataInterface $metadata
     * @param null|SegmentInterface $segment
     * @return string
     */
    public function execute(FileInterface $file, MetadataInterface $metadata, ?SegmentInterface $segment): FileInterface
    {
        if (is_callable('iptcembed') && is_callable('iptcparse')) {
            $iptcData =  $segment ? iptcparse($segment->getData()) : [];

            if (!empty($metadata->getTitle())) {
                $iptcData[self::IPTC_TITLE_SEGMENT][0] = $metadata->getTitle();
            }

            if (!empty($metadata->getDescription())) {
                $iptcData[self::IPTC_DESCRIPTION_SEGMENT][0] = $metadata->getDescription();
            }

            if (!empty($metadata->getKeywords())) {
                foreach ($metadata->getKeywords() as $key => $keyword) {
                    $iptcData[self::IPTC_KEYWORDS_SEGMENT][$key] = $keyword;
                }
            }

            $newData = '';

            foreach ($iptcData as $tag => $values) {
                foreach ($values as $value) {
                    $newData .= $this->iptcMaketag(2, (int) substr($tag, 2), $value);
                }
            }

            $content = iptcembed($newData, $file->getPath());
            $resource = $this->driver->fileOpen($file->getPath(), 'wb');
            
            $this->driver->fileWrite($resource, $content);
            $this->driver->fileClose($resource);
            
            return $this->fileReader->execute($file->getPath());
        }
    }

    /**
     * Create new iptc tag text
     *
     * @param int $rec
     * @param int $tag
     * @param string $value
     */
    private function iptcMaketag(int $rec, int $tag, string $value)
    {
        //phpcs:disable Magento2.Functions.DiscouragedFunction
        $length = strlen($value);
        $retval = chr(0x1C) . chr($rec) . chr($tag);

        if ($length < 0x8000) {
            $retval .= chr($length >> 8) .  chr($length & 0xFF);
        } else {
            $retval .= chr(0x80) .
                   chr(0x04) .
                   chr(($length >> 24) & 0xFF) .
                   chr(($length >> 16) & 0xFF) .
                   chr(($length >> 8) & 0xFF) .
                   chr($length & 0xFF);
        }
        //phpcs:enable Magento2.Functions.DiscouragedFunction
        return $retval . $value;
    }
}
