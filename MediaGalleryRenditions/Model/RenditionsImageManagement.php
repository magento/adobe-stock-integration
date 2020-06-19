<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Magento\MediaGalleryRenditions\Model;

use Magento\Framework\App\Filesystem\DirectoryList;
use Magento\Framework\App\ObjectManager;
use Magento\Framework\Exception\FileSystemException;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Filesystem;
use Magento\Framework\Filesystem\DriverInterface;
use Magento\Framework\Filesystem\Io\File;

class RenditionsImageManagement
{
    /**
     * @var Filesystem\Directory\WriteInterface
     */
    private $_directory;

    /**
     * @var File|mixed|null
     */
    private $ioFile;

    /**
     * @var DriverInterface|mixed|null
     */
    private $file;

    private const RENDITIONS_DIRECTORY_NAME = '.renditions';

    /**
     * @param Filesystem $filesystem
     * @param File|null $ioFile
     * @param DriverInterface|null $file
     * @throws FileSystemException
     */
    public function __construct(
        Filesystem $filesystem,
        File $ioFile = null,
        DriverInterface $file = null
    ) {
        $this->_directory = $filesystem->getDirectoryWrite(DirectoryList::MEDIA);
        $this->ioFile = $ioFile ?: ObjectManager::getInstance()->get(File::class);
        $this->file = $file ?: ObjectManager::getInstance()->get(Filesystem\Driver\File::class);
    }

    /**
     * Returns Rendition image path
     *
     * @param string $path
     * @return string
     * @throws LocalizedException
     * @throws FileSystemException
     */
    public function execute(string $path) :string
    {
        $realPath = $this->_directory->getRelativePath($path);
        if (!$this->_directory->isFile($realPath) || !$this->_directory->isExist($realPath)) {
            throw new LocalizedException(__('Directory or File %1 does not exist in media directory.', $realPath));
        }
        $renditionImageDirectoryPath = $this->getRenditionsImageDirectory($path);
        $renditionImageDirectory = $this->_directory->getRelativePath($renditionImageDirectoryPath);
        if (!$this->_directory->isExist($renditionImageDirectory)) {
            $this->_directory->create($renditionImageDirectory);
        }
        if (!$this->_directory->isExist($renditionImageDirectory)) {
            throw new LocalizedException(__(
                'Directory %1 does not exist in media directory.',
                $renditionImageDirectory
            ));
        }
        return $renditionImageDirectoryPath . '/' . $this->ioFile->getPathInfo($path)['basename'];
    }

    /**
     * Return renditions directory path for file/current directory
     *
     * @param bool|string $filePath Path to the file
     * @return string
     */
    private function getRenditionsImageDirectory($filePath = false) :string
    {
        $renditionRootDir = $this->getRenditionsRoot();

        if ($filePath) {
            $renditionImagePath = $this->getRenditionsPath($filePath, false);
            if ($renditionImagePath) {
                $renditionImageDir = $this->file->getParentDirectory($renditionImagePath);
            }
        }

        return $renditionImageDir ?? $renditionRootDir;
    }

    /**
     * Renditions root directory getter
     *
     * @return string
     */
    private function getRenditionsRoot() :string
    {
        return $this->_directory->getAbsolutePath() . self::RENDITIONS_DIRECTORY_NAME;
    }

    /**
     * Renditions path getter
     *
     * @param  string $filePath original file path
     * @param  bool $checkFile OPTIONAL is it necessary to check file availability
     * @return string|false
     */
    private function getRenditionsPath($filePath, $checkFile = false)
    {
        $mediaRootDir = $this->_directory->getAbsolutePath('');
        if (strpos($filePath, (string) $mediaRootDir) === 0) {
            $relativeFilePath = substr($filePath, strlen($mediaRootDir));
            // phpcs:ignore Magento2.Functions.DiscouragedFunction
            $renditionPath = $relativeFilePath === basename($filePath)
                ? $this->getRenditionsRoot() . DIRECTORY_SEPARATOR . $relativeFilePath
                : $this->getRenditionsRoot() . $relativeFilePath;
            if (!$checkFile || $this->_directory->isExist($this->_directory->getRelativePath($renditionPath))) {
                return $renditionPath;
            }
        }

        return false;
    }
}
