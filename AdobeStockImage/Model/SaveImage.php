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
use Magento\MediaGalleryApi\Model\Asset\Command\GetByPathInterface;
use Magento\MediaGalleryApi\Model\Keyword\Command\SaveAssetKeywordsInterface;
use Psr\Log\LoggerInterface;

/**
 * Save an image provided with the adobe Stock integration.
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 * @SuppressWarnings(PHPMD.ExcessiveParameterList)
 */
class SaveImage implements SaveImageInterface
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
     * @var SaveAssetKeywordsInterface
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
     * @var GetByPathInterface
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
     * SaveImage constructor.
     *
     * @param SaveAssetInterface $saveAdobeStockAsset
     * @param DocumentToAsset $documentToAsset
     * @param SaveAssetKeywordsInterface $saveAssetKeywords
     * @param DocumentToKeywords $documentToKeywords
     * @param SaveImageFile $saveImageFile
     * @param SaveMediaGalleryAsset $saveMediaGalleryAsset
     * @param GetByPathInterface $getMediaGalleryAssetByPath
     * @param AssetRepositoryInterface $assetRepository
     * @param LoggerInterface $logger
     */
    public function __construct(
        SaveAssetInterface $saveAdobeStockAsset,
        DocumentToAsset $documentToAsset,
        SaveAssetKeywordsInterface $saveAssetKeywords,
        DocumentToKeywords $documentToKeywords,
        SaveImageFile $saveImageFile,
        SaveMediaGalleryAsset $saveMediaGalleryAsset,
        GetByPathInterface $getMediaGalleryAssetByPath,
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
     */
    public function execute(Document $document, string $url, string $destinationPath): void
    {
        try {
            $this->saveImageFile->execute($document, $url, $destinationPath);
            $this->saveMediaGalleryAsset->execute($document, $destinationPath);
            $mediaAsset = $this->getMediaGalleryAssetByPath->execute($destinationPath);
            $mediaAssetId = $mediaAsset->getId();
            if (!$mediaAssetId) {
                /** @var AssetInterface $mediaAsset */
                $mediaAsset = $this->assetRepository->getById($document->getId());
                $mediaAssetId = $mediaAsset->getMediaGalleryId();
            }

            $keywords = $this->documentToKeywords->convert($document);
            $this->saveAssetKeywords->execute($keywords, $mediaAssetId);

            $asset = $this->documentToAsset->convert($document, ['media_gallery_id' => $mediaAssetId]);
            $this->saveAdobeStockAsset->execute($asset);
        } catch (LocalizedException $localizedException) {
            throw $localizedException;
        } catch (\Exception $exception) {
            $this->logger->critical($exception);
            throw new CouldNotSaveException(__('Could not save image.'), $exception);
        }
    }
}
