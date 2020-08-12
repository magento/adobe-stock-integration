<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Magento\MediaGalleryRenditions\Model;

use Magento\Framework\App\Filesystem\DirectoryList;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Filesystem;
use Magento\Framework\Filesystem\Driver\File;
use Magento\Framework\Image\AdapterFactory;
use Magento\MediaGalleryRenditionsApi\Api\GenerateRenditionsInterface;
use Magento\MediaGalleryRenditionsApi\Api\GetRenditionPathInterface;

class GenerateRenditions implements GenerateRenditionsInterface
{
    /**
     * @var AdapterFactory
     */
    private $imageFactory;

    /**
     * @var Config
     */
    private $config;

    /**
     * @var GetRenditionPathInterface
     */
    private $getRenditionPath;

    /**
     * @var Filesystem
     */
    private $filesystem;

    /**
     * @var IsRenditionRequired
     */
    private $isRenditionRequired;

    /**
     * @var File
     */
    private $driver;

    /**
     * @param AdapterFactory $imageFactory
     * @param Config $config
     * @param GetRenditionPathInterface $getRenditionPath
     * @param Filesystem $filesystem
     * @param File $driver
     * @param IsRenditionRequired $isRenditionRequired
     */
    public function __construct(
        AdapterFactory $imageFactory,
        Config $config,
        GetRenditionPathInterface $getRenditionPath,
        Filesystem $filesystem,
        File $driver,
        IsRenditionRequired $isRenditionRequired
    ) {
        $this->imageFactory = $imageFactory;
        $this->config = $config;
        $this->getRenditionPath = $getRenditionPath;
        $this->filesystem = $filesystem;
        $this->isRenditionRequired = $isRenditionRequired;
        $this->driver = $driver;
    }

    /**
     * @inheritdoc
     */
    public function execute(array $paths): void
    {
        foreach ($paths as $path) {
            $mediaDirectory = $this->filesystem->getDirectoryRead(DirectoryList::MEDIA);
            $absolutePath = $mediaDirectory->getAbsolutePath($path);
            if (!$this->isRenditionRequired->execute($absolutePath)) {
                continue;
            }

            $renditionPath = $this->getRenditionPath->execute($path);
            $this->createDirectory($renditionPath);

            try {
                $this->createRendition($absolutePath, $mediaDirectory->getAbsolutePath($renditionPath));
            } catch (\Exception $exception) {
                throw new LocalizedException(
                    __('Cannot create rendition for media asset %path', ['path' => $path])
                );
            }
        }
    }

    /**
     * Create directory for rendition file
     *
     * @param string $path
     * @throws LocalizedException
     */
    private function createDirectory(string $path): void
    {
        try {
            $this->filesystem->getDirectoryWrite(DirectoryList::MEDIA)
                ->create($this->driver->getParentDirectory($path));
        } catch (\Exception $exception) {
            throw new LocalizedException(__('Cannot create directory for rendition %path', ['path' => $path]));
        }
    }

    /**
     * Create rendition file
     *
     * @param string $absolutePath
     * @param string $absoluteRenditionPath
     * @throws \Exception
     */
    private function createRendition(string $absolutePath, string $absoluteRenditionPath): void
    {
        $image = $this->imageFactory->create();
        $image->open($absolutePath);
        $image->keepAspectRatio(true);
        $image->resize($this->config->getWidth(), $this->config->getHeight());
        $image->save($absoluteRenditionPath);
    }
}
