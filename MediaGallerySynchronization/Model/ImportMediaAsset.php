<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Magento\MediaGallerySynchronization\Model;

use Magento\MediaGallerySynchronizationApi\Api\ImportFileInterface;
use Magento\MediaGalleryApi\Api\SaveAssetsInterface;

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
     * @var GetAssetFromPath
     */
    private $getAssetFromPath;

    /**
     * @param SaveAssetsInterface $saveAssets
     * @param GetAssetFromPath $getAssetFromPath
     */
    public function __construct(
        SaveAssetsInterface $saveAssets,
        GetAssetFromPath $getAssetFromPath
    ) {
        $this->saveAssets = $saveAssets;
        $this->getAssetFromPath = $getAssetFromPath;
    }

    /**
     * @inheritdoc
     */
    public function execute(string $path): void
    {
        $this->saveAssets->execute([$this->getAssetFromPath->execute($path)]);
    }
}
