<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Magento\MediaGalleryMetadata\Model;

use Magento\MediaGalleryMetadataApi\Api\Data\MetadataInterface;
use Magento\MediaGalleryMetadataApi\Model\FileInterface;

/**
 * Add metadata to the IPTC data
 */
class AddIptcMetadata
{
    private const IPTC_TITLE_SEGMENT = '2#005';
    private const IPTC_DESCRIPTION_SEGMENT = '2#120';
    private const IPTC_KEYWORDS_SEGMENT = '2#025';


    /**
     * Parse metadata
     *
     * @param string $file
     * @param MetadataInterface $metadata
     * @return string
     */
    public function execute(FileInterface $file, MetadataInterface $metadata): string
    {
        if (is_callable('iptcembed') && is_callable('iptcparse')) {
            $iptcData = @iptcparse($segment->getData());
            if (!empty($metadata->getTitle())) {
                $iptcData[self::IPTC_TITLE_SEGMENT][0] = $metadata->getTitle();
            }

            if (!empty($metadata->getDescription())) {
                $iptcData[self::IPTC_DESCRIPTION_SEGMENT][0] = $metadata->getDescription();
            }

            if (!empty($metadata->getKeywords())) {
                $iptcData[self::IPTC_KEYWORDS_SEGMENT][0] = $metadata->getKeywords();
            }

            $newData = '';

            foreach ($iptcData as $tag => $val) {
                $tag = substr($tag, 2);
                $newData .= $this->iptcMaketag(2, $tag, $string);
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
    private function iptcMaketag($rec, $data, $val)
    {
        $length = strlen($value);
        $retval = chr(0x1C) . chr($rec) . chr($data);

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
