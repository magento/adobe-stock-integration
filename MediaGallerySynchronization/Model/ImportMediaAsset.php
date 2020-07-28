<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Magento\MediaGallerySynchronization\Model;

use Magento\MediaGallerySynchronizationApi\Api\ImportFileInterface;
use Magento\MediaGalleryApi\Api\SaveAssetsInterface;
use Magento\MediaGallerySynchronization\Model\Filesystem\SplFileInfoFactory;
use Magento\MediaGalleryRenditionsApi\Api\GenerateRenditionsInterface;

/**
 * Import image file to the media gallery asset table
 */
class ImportMediaAsset implements ImportFileInterface
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
     * @var GenerateRenditionsInterface
     */
    private $generateRenditions;

    /**
     * @param SplFileInfoFactory $splFileInfoFactory
     * @param SaveAssetsInterface $saveAssets
     * @param CreateAssetFromFile $createAssetFromFile
     * @param GenerateRenditionsInterface $generateRenditions
     */
    public function __construct(
        SplFileInfoFactory $splFileInfoFactory,
        SaveAssetsInterface $saveAssets,
        CreateAssetFromFile $createAssetFromFile,
        GenerateRenditionsInterface $generateRenditions
    ) {
        $this->splFileInfoFactory = $splFileInfoFactory;
        $this->saveAssets = $saveAssets;
        $this->createAssetFromFile = $createAssetFromFile;
        $this->generateRenditions = $generateRenditions;
    }

    /**
     * @inheritdoc
     */
    public function execute(string $path): void
    {
        $file = $this->splFileInfoFactory->create($path);
        $createdAsset = $this->createAssetFromFile->execute($file);
        $this->saveAssets->execute([$createdAsset]);
        $this->generateRenditions->execute([$createdAsset]);
    }
}
