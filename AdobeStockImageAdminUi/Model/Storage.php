<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

declare(strict_types=1);

namespace Magento\AdobeStockImageAdminUi\Model;

use Magento\Framework\App\Filesystem\DirectoryList;
use Magento\Framework\Exception\FileSystemException;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Filesystem\Io\File;
use Magento\MediaStorage\Helper\File\Storage as HelperStorage;
use Magento\MediaStorage\Model\File\Uploader;
use Magento\MediaStorage\Model\File\UploaderFactory;

/**
 * Class Preview
 */
class Storage
{
    /**
     * @var File
     */
    private $fileSystemIo;

    /**
     * @var DirectoryList
     */
    private $directoryList;

    /**
     * @var HelperStorage
     */
    private $storageHelper;

    /**
     * File constructor
     * @param DirectoryList $directoryList
     * @param File          $fileSystemIo
     * @param HelperStorage $storageHelper
     */
    public function __construct(
        DirectoryList $directoryList,
        File $fileSystemIo,
        HelperStorage $storageHelper
    ) {
        $this->directoryList = $directoryList;
        $this->fileSystemIo = $fileSystemIo;
        $this->storageHelper = $storageHelper;
    }

    /**
     * @param string $imageUrl
     * @throws LocalizedException
     */
    public function savePreview($imageUrl)
    {
        try {
            $filePath = $this->downloadToFileSystem($imageUrl);

            $this->storageHelper->processStorageFile($filePath);
        } catch (\Exception $e) {
            throw new LocalizedException(__('We can\'t save the preview file right now.'), $e);
        }
    }

    /**
     * @param string $imageUrl
     * @return string
     */
    private function getBaseName($imageUrl)
    {
        return $this->fileSystemIo->getPathInfo($imageUrl)['basename'];
    }

    /**
     * @return string
     * @throws FileSystemException
     */
    private function getMediaDir()
    {
        return $this->directoryList->getPath(DirectoryList::MEDIA);
    }

    /**
     * @param $imageUrl
     * @return string
     * @throws FileSystemException
     */
    private function downloadToFileSystem($imageUrl): string
    {
        $newFilePath = $this->getMediaDir() . DIRECTORY_SEPARATOR . $this->getBaseName($imageUrl);

        /** Read file from URL and copy to the temp destination */
        $this->fileSystemIo->read($imageUrl, $newFilePath);

        return $newFilePath;
    }
}
