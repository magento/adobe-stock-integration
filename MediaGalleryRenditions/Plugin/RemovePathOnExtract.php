<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Magento\MediaGalleryRenditions\Plugin;

use Magento\MediaContent\Model\ExtractAssetsFromContent;

/**
 * Remove rendition directory on content extract
 */
class RemovePathOnExtract
{
    private const RENDITIONS_DIRECTORY_NAME = '.renditions';

    /**
     * Remove renditions directory from path
     *
     * @param ExtractAssetsFromContent $subject
     * @param string $content
     * @return array
     */
    public function beforeExecute(ExtractAssetsFromContent $subject, string $content): array
    {
        $content = str_replace(
            self::RENDITIONS_DIRECTORY_NAME . '/',
            '',
            $content
        );
        return [$content];
    }
}
