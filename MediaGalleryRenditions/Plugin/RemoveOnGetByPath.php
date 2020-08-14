<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Magento\MediaGalleryRenditions\Plugin;

use Magento\MediaGallery\Model\Asset\Command\GetByPath;

/**
 * Remove rendition directory from path
 */
class RemoveOnGetByPath
{
    private const RENDITIONS_DIRECTORY_NAME = '.renditions';

    /**
     * Remove renditions directory from path
     *
     * @param GetByPath $subject
     * @param string $path
     * @return array
     */
    public function beforeExecute(GetByPath $subject, string $path): array
    {
        $path = ltrim($path, self::RENDITIONS_DIRECTORY_NAME . '/');

        return [$path];
    }
}
