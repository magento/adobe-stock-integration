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

/**
 * Add metadata to the IPTC data
 */
class AddIptcMetadata
{
    private const IPTC_TITLE_SEGMENT = '2#005';
    private const IPTC_DESCRIPTION_SEGMENT = '2#120';
    private const IPTC_KEYWORDS_SEGMENT = '2#025';


    /**
     * Write metadata
     *
     * @param FileInterface $file
     * @param MetadataInterface $metadata
     * @param  SegmentInterface $segment
     * @return string
     */
    public function execute(FileInterface $file, MetadataInterface $metadata, SegmentInterface $segment): string
    {
        if (is_callable('iptcembed')) {
            $iptcData = @iptcparse($segment->getData());
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
                    $newData .= $this->iptcMaketag(2, substr($tag, 2), $value);
                }
            }
            $content = @iptcembed($newData, $file->getPath());

            return $content;
        }
    }

    /**
     * Create new iptc tag text
     *
     * @param int $rec
     * @param string $tag
     * @param string $val
     */
    private function iptcMaketag($rec, $data, $value)
    {
        $length = strlen($value);
        $retval = chr(0x1C) . chr($rec) . chr((int)$data);

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

        return $retval . $value;
    }
}
