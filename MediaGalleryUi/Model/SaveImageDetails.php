<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Magento\MediaGalleryUi\Model;

use Magento\MediaGalleryApi\Api\Data\AssetInterfaceFactory;
use Magento\MediaGalleryApi\Api\GetAssetsByIdsInterface;
use Magento\MediaGalleryApi\Api\SaveAssetsInterface;
use Magento\MediaGalleryUi\Model\ProcessImageDetails\ProcessKeywords;
use Magento\MediaGalleryUi\Model\ProcessImageDetails\ProcessMetadata;

class SaveImageDetails
{
    /**
     * @var AssetInterfaceFactory
     */
    private $assetFactory;

    /**
     * @var GetAssetsByIdsInterface
     */
    private $getAssetsByIds;

    /**
     * @var SaveAssetsInterface
     */
    private $saveAssets;

    /**
     * @var ProcessMetadata
     */
    private $processMetadata;

    /**
     * @var ProcessKeywords
     */
    private $processKeywords;

    /**
     * @param AssetInterfaceFactory $assetFactory
     * @param GetAssetsByIdsInterface $getAssetsByIds
     * @param SaveAssetsInterface $saveAssets
     * @param ProcessKeywords $processKeywords
     * @param ProcessMetadata $processMetadata
     */
    public function __construct(
        AssetInterfaceFactory $assetFactory,
        GetAssetsByIdsInterface $getAssetsByIds,
        SaveAssetsInterface $saveAssets,
        ProcessKeywords $processKeywords,
        ProcessMetadata $processMetadata
    ) {
        $this->assetFactory = $assetFactory;
        $this->getAssetsByIds = $getAssetsByIds;
        $this->saveAssets = $saveAssets;
        $this->processKeywords = $processKeywords;
        $this->processMetadata = $processMetadata;
    }

    /**
     * Save image details
     *
     * @param int $imageId
     * @param array $imageKeywords
     * @param string $imageTitle
     * @param string $imageDescription
     */
    public function execute(int $imageId, array $imageKeywords, string $imageTitle, string $imageDescription): void
    {
        $asset = current($this->getAssetsByIds->execute([$imageId]));
        $updatedAsset = $this->assetFactory->create(
            [
                'path' => $asset->getPath(),
                'contentType' => $asset->getContentType(),
                'width' => $asset->getWidth(),
                'height' => $asset->getHeight(),
                'size' => $asset->getSize(),
                'id' => $asset->getId(),
                'title' => $imageTitle,
                'description' => $imageDescription,
                'source' => $asset->getSource(),
                'hash' => $asset->getHash(),
                'created_at' => $asset->getCreatedAt(),
                'updated_at' => $asset->getUpdatedAt()
            ]
        );

        $this->saveAssets->execute([$updatedAsset]);
        $this->processMetadata->execute($asset->getPath(), $imageTitle, $imageDescription, $imageKeywords);
        $this->processKeywords->execute($imageKeywords, $imageId);
    }
}
