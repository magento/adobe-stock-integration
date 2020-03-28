<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Magento\MediaGalleryUi\Model\Filesystem;

class GetSplFileInfo
{
    /**
     * Method return SplFileInfo from filename
     *
     * @param string $fileName
     * @return \SplFileInfo
     */
    public function execute(string $fileName) : \SplFileInfo
    {
        return $this->createSplFileInfo($fileName);
    }

    /**
     * Factory method
     *
     * @param string $fileName
     * @return \SplFileInfo
     */
    private function createSplFileInfo(string $fileName) : \SplFileInfo
    {
        return new \SplFileInfo($fileName);
    }
}
