<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Magento\MediaGallerySynchronization\Model;

use Magento\Framework\App\Filesystem\DirectoryList;
use Magento\Framework\Filesystem;
use Magento\MediaGalleryApi\Api\SaveAssetsInterface;
use Magento\MediaGallerySynchronization\Model\Filesystem\SplFileInfoFactory;
use Magento\MediaGallerySynchronizationApi\Model\ImportFilesInterface;

/**
 * Import image file to the media gallery asset table
 */
class ImportMediaAsset implements ImportFilesInterface
{
    /**
     * @var SaveAssetsInterface
     */
    private $saveAssets;

    /**
     * @var SplFileInfoFactory
     */
    private $splFileInfoFactory;

    /**
     * @var CreateAssetFromFile
     */
    private $createAssetFromFile;

    /**
     * @var Filesystem
     */
    private $filesystem;

    /**
     * @param SplFileInfoFactory $splFileInfoFactory
     * @param SaveAssetsInterface $saveAssets
     * @param CreateAssetFromFile $createAssetFromFile
     * @param Filesystem $filesystem
     */
    public function __construct(
        SplFileInfoFactory $splFileInfoFactory,
        SaveAssetsInterface $saveAssets,
        CreateAssetFromFile $createAssetFromFile,
        Filesystem $filesystem
    ) {
        $this->splFileInfoFactory = $splFileInfoFactory;
        $this->saveAssets = $saveAssets;
        $this->createAssetFromFile = $createAssetFromFile;
        $this->filesystem = $filesystem;
    }

    /**
     * @inheritdoc
     */
    public function execute(array $paths): void
    {
        $assets = [];

        foreach ($paths as $path) {
            $assets[] = $this->createAssetFromFile->execute(
                $this->splFileInfoFactory->create(
                    $this->filesystem->getDirectoryRead(DirectoryList::MEDIA)->getAbsolutePath($path)
                )
            );
        }

        $this->saveAssets->execute($assets);
    }
}
