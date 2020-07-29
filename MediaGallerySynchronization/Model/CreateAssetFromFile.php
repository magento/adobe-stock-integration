<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Magento\MediaGallerySynchronization\Model;

use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Exception\ValidatorException;
use Magento\MediaGalleryApi\Api\Data\AssetInterface;
use Magento\MediaGalleryApi\Api\Data\AssetInterfaceFactory;
use Magento\MediaGalleryApi\Api\GetAssetsByPathsInterface;
use Magento\MediaGalleryMetadataApi\Api\ExtractMetadataInterface;

/**
 * Create media asset object based on the file information
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class CreateAssetFromFile
{
    /**
     * @var GetAssetsByPathsInterface
     */
    private $getMediaGalleryAssetByPath;

    /**
     * @var AssetInterfaceFactory
     */
    private $assetFactory;

    /**
     * @var UpdateAsset
     */
    private $updateAsset;

    /**
     * @var ExtractMetadataInterface
     */
    private $extractMetadata;

    /**
     * @param AssetInterfaceFactory $assetFactory
     * @param GetAssetsByPathsInterface $getMediaGalleryAssetByPath
     * @param UpdateAsset $updateAsset
     * @param ExtractMetadataInterface $extractMetadata
     */
    public function __construct(
        AssetInterfaceFactory $assetFactory,
        GetAssetsByPathsInterface $getMediaGalleryAssetByPath,
        UpdateAsset $updateAsset,
        ExtractMetadataInterface $extractMetadata
    ) {
        $this->assetFactory = $assetFactory;
        $this->getMediaGalleryAssetByPath = $getMediaGalleryAssetByPath;
        $this->updateAsset = $updateAsset;
        $this->extractMetadata = $extractMetadata;
    }

    /**
     * Create media asset object based on the file information
     *
     * @param \SplFileInfo $file
     * @return AssetInterface
     * @throws LocalizedException
     * @throws ValidatorException
     */
    public function execute(\SplFileInfo $file): AssetInterface
    {
        $path = $file->getPath() . '/' . $file->getFileName();
        $asset = $this->getAsset($path);
        $metadata = $this->extractMetadata->execute($path);
        $updatedAsset = $this->updateAsset->execute($file, $asset, $metadata);

        return $updatedAsset;
    }

    /**
     * Returns asset if asset already exist by provided path
     *
     * @param string $path
     * @return AssetInterface|null
     * @throws ValidatorException
     * @throws LocalizedException
     */
    private function getAsset(string $path): ?AssetInterface
    {
        $asset = $this->getMediaGalleryAssetByPath->execute([$this->updateAsset->getRelativePath($path)]);
        return !empty($asset) ? $asset[0] : null;
    }
}
