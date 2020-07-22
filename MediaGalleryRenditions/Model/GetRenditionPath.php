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
use Magento\MediaGalleryApi\Api\Data\AssetInterface;
use Magento\MediaGalleryRenditionsApi\Api\GetRenditionPathInterface;

class GetRenditionPath implements GetRenditionPathInterface
{
    /**
     * @var Filesystem\Directory\WriteInterface
     */
    private $directory;

    /**
     * @var DriverInterface|mixed|null
     */
    private $file;

    private const RENDITIONS_DIRECTORY_NAME = '.renditions';

    /**
     * @param Filesystem $filesystem
     * @param DriverInterface|null $file
     * @throws FileSystemException
     */
    public function __construct(
        Filesystem $filesystem,
        DriverInterface $file = null
    ) {
        $this->directory = $filesystem->getDirectoryRead(DirectoryList::MEDIA);
        $this->file = $file ?: ObjectManager::getInstance()->get(Filesystem\Driver\File::class);
    }

    /**
     * Returns Rendition image path
     *
     * @param AssetInterface $asset
     * @return string
     * @throws LocalizedException
     */
    public function execute(AssetInterface $asset) :string
    {
        return $this->getRenditionsImageDirectory($asset->getPath());
    }

    /**
     * Return renditions directory path for file/current directory
     *
     * @param string $filePath Path to the file
     * @return string
     */
    private function getRenditionsImageDirectory(string $filePath) :string
    {
        $renditionRootDir = $this->getRenditionsRoot();

        if ($filePath) {
            $renditionImagePath = $this->getRenditionsPath($filePath);
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
        return $this->directory->getAbsolutePath() . self::RENDITIONS_DIRECTORY_NAME;
    }

    /**
     * Renditions path getter
     *
     * @param  string $filePath original file path
     * @return string|false
     */
    private function getRenditionsPath($filePath) :?string
    {
        $mediaRootDir = $this->directory->getAbsolutePath('');
        if (strpos($filePath, (string) $mediaRootDir) === 0) {
            $relativeFilePath = substr($filePath, strlen($mediaRootDir));
            // phpcs:ignore Magento2.Functions.DiscouragedFunction
            $renditionPath = $this->getRenditionsRoot();
            if ($relativeFilePath === basename($filePath)) {
                $renditionPath .= DIRECTORY_SEPARATOR;
            }
            $renditionPath .= $relativeFilePath;
            return $renditionPath;
        }

        return null;
    }
}
