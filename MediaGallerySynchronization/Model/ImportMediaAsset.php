<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Magento\MediaGallerySynchronization\Model;

use Magento\MediaGalleryApi\Api\SaveAssetsInterface;
use Magento\MediaGallerySynchronization\Model\Filesystem\SplFileInfoFactory;
use Magento\MediaGallerySynchronizationApi\Api\ImportFileInterface;

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
     * @param SplFileInfoFactory $splFileInfoFactory
     * @param SaveAssetsInterface $saveAssets
     * @param CreateAssetFromFile $createAssetFromFile
     */
    public function __construct(
        SplFileInfoFactory $splFileInfoFactory,
        SaveAssetsInterface $saveAssets,
        CreateAssetFromFile $createAssetFromFile
    ) {
        $this->splFileInfoFactory = $splFileInfoFactory;
        $this->saveAssets = $saveAssets;
        $this->createAssetFromFile = $createAssetFromFile;
    }

    /**
     * @inheritdoc
     */
    public function execute(string $path): void
    {
        $this->saveAssets->execute(
            [
                $this->createAssetFromFile->execute(
                    $this->splFileInfoFactory->create($path)
                )
            ]
        );
    }
}
