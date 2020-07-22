<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magento\MediaGalleryUi\Model;

use Magento\Framework\App\Filesystem\DirectoryList;
use Magento\Framework\Exception\FileSystemException;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Filesystem;
use Magento\MediaGalleryApi\Api\Data\AssetInterfaceFactory;
use Magento\MediaGalleryApi\Api\Data\AssetKeywordsInterfaceFactory;
use Magento\MediaGalleryApi\Api\Data\KeywordInterfaceFactory;
use Magento\MediaGalleryApi\Api\GetAssetsByIdsInterface;
use Magento\MediaGalleryApi\Api\SaveAssetsInterface;
use Magento\MediaGalleryApi\Api\SaveAssetsKeywordsInterface;
use Magento\MediaGalleryMetadataApi\Api\AddMetadataInterface;
use Magento\MediaGalleryMetadataApi\Api\Data\MetadataInterfaceFactory;
use Psr\Log\LoggerInterface;

class SaveImageDetails
{
    /**
     * @ var Filesystem
     */
    private $fileSystem;

    /**
     * @var AssetInterfaceFactory
     */
    private $assetFactory;

    /**
     * @var AssetKeywordsInterfaceFactory
     */
    private $assetKeywordsFactory;

    /**
     * @var KeywordInterfaceFactory
     */
    private $keywordFactory;

    /**
     * @var GetAssetsByIdsInterface
     */
    private $getAssetsByIds;

    /**
     * @var SaveAssetsInterface
     */
    private $saveAssets;

    /**
     * @var SaveAssetsKeywordsInterface
     */
    private $saveAssetKeywords;

    /**
     * @var AddMetadataInterface
     */
    private $addMetadata;

    /**
     * @var MetadataInterfaceFactory
     */
    private $metadataFactory;

    /**
     * @var LoggerInterface
     */
    private $logger;

    /**
     * SaveImageDetails constructor.
     *
     * @param Filesystem $fileSystem
     * @param AssetInterfaceFactory $assetFactory
     * @param AssetKeywordsInterfaceFactory $assetKeywordsFactory
     * @param KeywordInterfaceFactory $keywordFactory
     * @param GetAssetsByIdsInterface $getAssetsByIds
     * @param SaveAssetsInterface $saveAssets
     * @param SaveAssetsKeywordsInterface $saveAssetKeywords
     * @param AddMetadataInterface $addMetadata
     * @param MetadataInterfaceFactory $metadataFactory
     * @param LoggerInterface|null $logger
     */
    public function __construct(
        Filesystem $fileSystem,
        AssetInterfaceFactory $assetFactory,
        AssetKeywordsInterfaceFactory $assetKeywordsFactory,
        KeywordInterfaceFactory $keywordFactory,
        GetAssetsByIdsInterface $getAssetsByIds,
        SaveAssetsInterface $saveAssets,
        SaveAssetsKeywordsInterface $saveAssetKeywords,
        AddMetadataInterface $addMetadata,
        MetadataInterfaceFactory $metadataFactory,
        LoggerInterface $logger
    ) {
        $this->fileSystem = $fileSystem;
        $this->assetFactory = $assetFactory;
        $this->assetKeywordsFactory = $assetKeywordsFactory;
        $this->keywordFactory = $keywordFactory;
        $this->getAssetsByIds = $getAssetsByIds;
        $this->saveAssets = $saveAssets;
        $this->saveAssetKeywords = $saveAssetKeywords;
        $this->addMetadata = $addMetadata;
        $this->metadataFactory = $metadataFactory;
        $this->logger = $logger;
    }

    /**
     * Save image details
     *
     * @param null|string $imageId
     * @param array $imageKeywords
     * @param null|string $imageTitle
     * @param null|string $imageDescription
     * @throws LocalizedException
     */
    public function execute($imageId, $imageKeywords, $imageTitle, $imageDescription): void
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

        $this->updateMetadata(
            $asset->getPath(),
            $imageTitle,
            $imageDescription,
            $imageKeywords
        );

        $this->saveAssets->execute([$updatedAsset]);

        $arrayKeywords = $this->convertKeywords($imageKeywords);
        $assetKeywords = $this->assetKeywordsFactory->create([
            'assetId' => $imageId,
            'keywords' => $arrayKeywords
        ]);
        $this->saveAssetKeywords->execute([$assetKeywords]);
    }

    /**
     * Save updated metadata
     *
     * @param object $imagePath
     * @param null|string $imageTitle
     * @param null|string $imageDescription
     * @param array $imageKeywords
     */
    private function updateMetadata(
        $imagePath,
        $imageTitle,
        $imageDescription,
        $imageKeywords
    ) {
        $mediaDirectory = $this->fileSystem->getDirectoryRead(DirectoryList::MEDIA);
        $filePath = $mediaDirectory->getAbsolutePath($imagePath);
        $metadata = $this->metadataFactory->create([
            'title' => $imageTitle,
            'description' => $imageDescription,
            'keywords' => $imageKeywords
        ]);

        try {
            $this->addMetadata->execute($filePath, $metadata);
        } catch (LocalizedException $e) {
            $this->logger->critical($e);
        }
    }

    /**
     * Convert keywords
     *
     * @param array $keywords
     * @return array
     */
    private function convertKeywords(array $keywords): array
    {
        $arrayKeywords = [];
        foreach ($keywords as $keyword) {
            $arrayKeywords[] = $this->keywordFactory->create(
                [
                    'keyword' => $keyword
                ]
            );
        }
        return $arrayKeywords;
    }
}
