<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Magento\MediaGalleryRenditions\Model;

use Magento\Framework\App\Filesystem\DirectoryList;
use Magento\Framework\Filesystem;
use Magento\Framework\Image\AdapterFactory;
use Magento\MediaGalleryApi\Api\Data\AssetInterface;
use Magento\MediaGalleryRenditionsApi\Api\GenerateRenditionsInterface;
use Magento\MediaGalleryRenditionsApi\Api\GetRenditionPathInterface;
use Magento\MediaGalleryRenditionsApi\Model\ConfigInterface;
use Magento\MediaGallerySynchronization\Model\CreateAssetFromFile;

class GenerateRenditions implements GenerateRenditionsInterface
{
    /**
     * @var AdapterFactory
     */
    private $imageFactory;

    /**
     * @var ConfigInterface
     */
    private $config;

    /**
     * @var GetRenditionPathInterface
     */
    private $getRenditionPath;

    /**
     * @var CreateAssetFromFile
     */
    private $createAssetFromFile;
    /**
     * @var Filesystem
     */
    private $filesystem;

    /**
     * GenerateRenditions constructor.
     * @param AdapterFactory $imageFactory
     * @param GetRenditionPathInterface $getRenditionPath
     * @param CreateAssetFromFile $createAssetFromFile
     * @param Filesystem $filesystem
     * @param ConfigInterface $config
     */
    public function __construct(
        AdapterFactory $imageFactory,
        GetRenditionPathInterface $getRenditionPath,
        CreateAssetFromFile $createAssetFromFile,
        Filesystem $filesystem,
        ConfigInterface $config
    ) {
        $this->imageFactory = $imageFactory;
        $this->config = $config;
        $this->getRenditionPath = $getRenditionPath;
        $this->createAssetFromFile = $createAssetFromFile;
        $this->filesystem = $filesystem;
    }

    /**
     * @inheritDoc
     */
    public function execute(array $assets): void
    {
        /* @var $asset AssetInterface */
        foreach ($assets as $asset) {
            $mediaDirectory = $this->filesystem->getDirectoryRead(DirectoryList::MEDIA);
            $path = $mediaDirectory->getAbsolutePath($asset->getPath());
            if (!$this->isResizeable($asset->getHeight(), $asset->getWidth())) {
                continue;
            }
            $renditionDirectoryPath = $this->getRenditionPath->execute($asset);
            $renditionImagePath = $renditionDirectoryPath . $asset->getPath();
            $image = $this->imageFactory->create();
            $image->open($path);
            $image->keepAspectRatio(true);
            $image->resize($this->getResizedWidth(), $this->getResizedHeight());
            $image->save($renditionImagePath);
        }
    }

    /**
     * Check if image needs to resize or not
     *
     * @param int $height
     * @param int $width
     * @return bool
     */
    private function isResizeable(int $height, int $width): bool
    {
        return $width > $this->getResizedWidth() || $height > $this->getResizedHeight();
    }

    /**
     * Get resized image width
     *
     * @return int
     */
    private function getResizedWidth(): int
    {
        return $this->config->getWidth();
    }

    /**
     * Get resized image height
     *
     * @return int
     */
    private function getResizedHeight() :int
    {
        return $this->config->getHeight();
    }
}
