<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

declare(strict_types=1);

namespace Magento\AdobeStockImage\Model\Storage;

use Magento\Framework\App\Filesystem\DirectoryList;
use Magento\Framework\Exception\AlreadyExistsException;
use Magento\Framework\Exception\FileSystemException;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Filesystem;
use Magento\Framework\Filesystem\Driver\Https;
use Magento\Framework\Filesystem\DriverInterface;
use Magento\MediaGalleryApi\Api\IsPathExcludedInterface;
use Magento\Cms\Model\Wysiwyg\Images\Storage;

/**
 * Save images to the file system
 */
class Save
{
    private const IMAGE_FILE_NAME_PATTERN = '#\.(jpg|jpeg|gif|png)$# i';

    /**
     * @var Filesystem
     */
    private $filesystem;

    /**
     * @var DriverInterface
     */
    private $driver;

    /**
     * @var IsPathExcludedInterface
     */
    private $isPathExcluded;

    /**
     * @var Storage
     */
    private $storage;

    /**
     * @param Filesystem $filesystem
     * @param Https $driver
     * @param IsPathExcludedInterface $isPathExcluded
     * @param Storage $storage
     */
    public function __construct(
        Filesystem $filesystem,
        Https $driver,
        IsPathExcludedInterface $isPathExcluded,
        Storage $storage
    ) {
        $this->filesystem = $filesystem;
        $this->driver = $driver;
        $this->isPathExcluded = $isPathExcluded;
        $this->storage = $storage;
    }

    /**
     * Save file from the URL to destination directory relative to media directory
     *
     * @param string $imageUrl
     * @param string $destinationPath
     * @throws AlreadyExistsException
     * @throws FileSystemException
     * @throws LocalizedException
     */
    public function execute(string $imageUrl, string $destinationPath): void
    {
        $mediaDirectory = $this->filesystem->getDirectoryWrite(DirectoryList::MEDIA);

        if ($this->isPathExcluded->execute($destinationPath)) {
            throw new LocalizedException(__('Could not save image: destination directory is restricted.'));
        }

        if (!preg_match(self::IMAGE_FILE_NAME_PATTERN, $destinationPath)) {
            throw new LocalizedException(__('Could not save image: unsupported file type.'));
        }

        if ($mediaDirectory->isExist($destinationPath)) {
            throw new AlreadyExistsException(__('Image with the same file name already exits.'));
        }

        $fileContents = $this->driver->fileGetContents($this->getUrlWithoutProtocol($imageUrl));
        $mediaDirectory->writeFile($destinationPath, $fileContents);
        $this->storage->resizeFile($mediaDirectory->getAbsolutePath($destinationPath));
    }

    /**
     * Remove the protocol from the url to use it for DriverInterface::fileGetContents
     *
     * @param string $imageUrl
     * @return string
     */
    private function getUrlWithoutProtocol(string $imageUrl): string
    {
        return str_replace('https://', '', $imageUrl);
    }
}
