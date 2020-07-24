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
use Magento\MediaGallerySynchronization\Model\CreateAssetFromFile;
use Magento\Framework\Filesystem\Driver\File;

class GenerateRenditions implements GenerateRenditionsInterface
{
    /**
     * @var AdapterFactory
     */
    private $imageFactory;

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
     * @var IsRenditionImageResizeable
     */
    private $isRenditionImageResizeable;

    /**
     * @var File
     */
    private $driver;

    /**
     * GenerateRenditions constructor.
     * @param AdapterFactory $imageFactory
     * @param GetRenditionPathInterface $getRenditionPath
     * @param CreateAssetFromFile $createAssetFromFile
     * @param Filesystem $filesystem
     * @param File $driver
     * @param IsRenditionImageResizeable $isRenditionImageResizeable
     */
    public function __construct(
        AdapterFactory $imageFactory,
        GetRenditionPathInterface $getRenditionPath,
        CreateAssetFromFile $createAssetFromFile,
        Filesystem $filesystem,
        File $driver,
        IsRenditionImageResizeable $isRenditionImageResizeable
    ) {
        $this->imageFactory = $imageFactory;
        $this->getRenditionPath = $getRenditionPath;
        $this->createAssetFromFile = $createAssetFromFile;
        $this->filesystem = $filesystem;
        $this->isRenditionImageResizeable = $isRenditionImageResizeable;
        $this->driver = $driver;
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
            if (!$this->isRenditionImageResizeable->execute($asset)) {
                continue;
            }
            $renditionImagePath = $this->getRenditionPath->execute($asset);
            $renditionDirectoryPath = $this->driver->getParentDirectory($renditionImagePath);
            $this->filesystem->getDirectoryWrite(DirectoryList::MEDIA)
                 ->create($renditionDirectoryPath);
            $image = $this->imageFactory->create();
            $image->open($path);
            $image->keepAspectRatio(true);
            $image->resize(
                $this->isRenditionImageResizeable->getResizedWidth(),
                $this->isRenditionImageResizeable->getResizedHeight()
            );
            $image->save($mediaDirectory->getAbsolutePath($renditionImagePath));
        }
    }
}
