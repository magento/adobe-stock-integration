<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Magento\MediaGalleryUi\Model\Filesystem;

class SplFileInfoFactory
{
    /**
     * Factory Method - creates SplFileInfo from filename
     *
     * @param string $fileName
     * @return \SplFileInfo
     */
    public function create(string $fileName) : \SplFileInfo
    {
        return new \SplFileInfo($fileName);
    }
}
