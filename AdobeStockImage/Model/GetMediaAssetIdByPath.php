<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Magento\AdobeStockImage\Model;

use Magento\AdobeStockAssetApi\Api\AssetRepositoryInterface;
use Magento\AdobeStockAssetApi\Api\Data\AssetInterface;
use Magento\AdobeStockAssetApi\Api\SaveAssetInterface;
use Magento\AdobeStockImage\Model\Extract\AdobeStockAsset as DocumentToAsset;
use Magento\AdobeStockImage\Model\Extract\Keywords as DocumentToKeywords;
use Magento\AdobeStockImageApi\Api\SaveImageInterface;
use Magento\Framework\Api\Search\Document;
use Magento\Framework\Exception\CouldNotSaveException;
use Magento\Framework\Exception\LocalizedException;
use Magento\MediaGalleryApi\Api\Data\AssetKeywordsInterfaceFactory;
use Magento\MediaGalleryApi\Api\GetAssetsByPathsInterface;
use Magento\MediaGalleryApi\Api\SaveAssetsKeywordsInterface;
use Psr\Log\LoggerInterface;

/**
 * Get media asset id by media asset path
 */
class GetMediaAssetIdByPath
{
    /**
     * @var SaveAssetInterface
     */
    private $saveAdobeStockAsset;

    /**
     * @var DocumentToAsset
     */
    private $documentToAsset;

    /**
     * @var DocumentToKeywords
     */
    private $documentToKeywords;

    /**
     * @var SaveAssetsKeywordsInterface
     */
    private $saveAssetKeywords;

    /**
     * @var SaveImageFile
     */
    private $saveImageFile;

    /**
     * @var SaveMediaGalleryAsset
     */
    private $saveMediaGalleryAsset;

    /**
     * @var GetAssetsByPathsInterface
     */
    private $getMediaGalleryAssetByPath;

    /**
     * @var AssetRepositoryInterface
     */
    private $assetRepository;

    /**
     * @var LoggerInterface
     */
    private $logger;

    /**
     * @param SaveAssetInterface $saveAdobeStockAsset
     * @param DocumentToAsset $documentToAsset
     * @param SaveKeywords $saveAssetKeywords
     * @param DocumentToKeywords $documentToKeywords
     * @param SaveImageFile $saveImageFile
     * @param SaveMediaGalleryAsset $saveMediaGalleryAsset
     * @param GetAssetsByPathsInterface $getMediaGalleryAssetByPath
     * @param AssetRepositoryInterface $assetRepository
     * @param LoggerInterface $logger
     */
    public function __construct(
        SaveAssetInterface $saveAdobeStockAsset,
        DocumentToAsset $documentToAsset,
        SaveKeywords $saveAssetKeywords,
        DocumentToKeywords $documentToKeywords,
        SaveImageFile $saveImageFile,
        SaveMediaGalleryAsset $saveMediaGalleryAsset,
        GetAssetsByPathsInterface $getMediaGalleryAssetByPath,
        AssetRepositoryInterface $assetRepository,
        LoggerInterface $logger
    ) {
        $this->saveAdobeStockAsset = $saveAdobeStockAsset;
        $this->documentToAsset = $documentToAsset;
        $this->saveAssetKeywords = $saveAssetKeywords;
        $this->documentToKeywords = $documentToKeywords;
        $this->saveImageFile = $saveImageFile;
        $this->saveMediaGalleryAsset = $saveMediaGalleryAsset;
        $this->getMediaGalleryAssetByPath = $getMediaGalleryAssetByPath;
        $this->assetRepository = $assetRepository;
        $this->logger = $logger;
    }

    /**
     * Downloads the image and save it to file system and data storage
     *
     * @param Document $document
     * @param string $url
     * @param string $destinationPath
     *
     * @throws CouldNotSaveException
     * @throws LocalizedException
     */
    public function execute(Document $document, string $url, string $destinationPath): void
    {
        $mediaAssets = $this->getMediaGalleryAssetByPath->execute([$destinationPath]);
        if (empty($mediaAssets)) {
            /** @var AssetInterface $mediaAsset */
            $adobeAsset = $this->assetRepository->getById($document->getId());
            $mediaAssetId = $adobeAsset->getMediaGalleryId();
        } else {
            $mediaAssetId = $mediaAssets[0]->getId();
        }
        return $mediaAssetId;
    }
}
