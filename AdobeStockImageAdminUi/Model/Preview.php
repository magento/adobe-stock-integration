<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

declare(strict_types=1);

namespace Magento\AdobeStockImageAdminUi\Model;

use Exception;
use Magento\Framework\App\Filesystem\DirectoryList;
use Magento\Framework\Exception\FileSystemException;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Filesystem\Io\File;

/**
 * Class Preview
 * Todo: Refactor to use saveImage Api from Magento_MediaStorage to enable filesystem and db storage.
 */
class Preview
{
    /**
     * @var File
     */
    private $file;

    /**
     * @var DirectoryList
     */
    private $directoryList;

    /**
     * File constructor
     * @param DirectoryList $directoryList
     * @param File          $file
     */
    public function __construct(
        DirectoryList $directoryList,
        File $file
    ) {
        $this->directoryList = $directoryList;
        $this->file = $file;
    }

    /**
     * @param string $imageUrl
     * @throws LocalizedException
     */
    public function saveFromUrl($imageUrl)
    {
        /** @var string $mediaDir */
        $mediaDir = $this->getMediaDir();

        $newFileName = $mediaDir . baseName($imageUrl);

        /** Read file from URL and copy it to the new destination */
        try {
            $this->file->read($imageUrl, $newFileName);
        } catch (Exception $e) {
            throw new LocalizedException(__('We can\'t save the preview file right now.'), $e);
        }
    }

    /**
     * Media directory name for the file storage
     *
     * @return string
     * @throws FileSystemException
     */
    private function getMediaDir()
    {
        return $this->directoryList->getPath(DirectoryList::MEDIA) . DIRECTORY_SEPARATOR;
    }
}
