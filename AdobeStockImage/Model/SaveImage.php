<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Magento\AdobeStockImage\Model;

use Magento\AdobeStockAssetApi\Api\SaveAssetInterface;
use Magento\AdobeStockImage\Model\Extract\AdobeStockAsset as DocumentToAsset;
use Magento\AdobeStockImage\Model\Extract\Keywords as DocumentToKeywords;
use Magento\AdobeStockImageApi\Api\SaveImageInterface;
use Magento\Framework\Api\Search\Document;
use Magento\Framework\Exception\CouldNotSaveException;
use Magento\Framework\Exception\LocalizedException;
use Magento\MediaGalleryApi\Api\GetAssetsByPathsInterface;
use Magento\MediaGalleryApi\Api\SaveAssetsKeywordsInterface;
use Psr\Log\LoggerInterface;

/**
 * Save an image provided with the adobe Stock integration.
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
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
     * @var SaveKeywords
     */
    private $saveKeywords;

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
        LoggerInterface $logger
    ) {
        $this->saveAdobeStockAsset = $saveAdobeStockAsset;
        $this->documentToAsset = $documentToAsset;
        $this->saveKeywords = $saveAssetKeywords;
        $this->documentToKeywords = $documentToKeywords;
        $this->saveImageFile = $saveImageFile;
        $this->saveMediaGalleryAsset = $saveMediaGalleryAsset;
        $this->getMediaGalleryAssetByPath = $getMediaGalleryAssetByPath;
        $this->logger = $logger;
    }

    /**
     * Downloads the image and save it to file system and data storage
     *
     * @param Document $document
     * @param string $url
     * @param string $destinationPath
     * @throws CouldNotSaveException
     * @throws LocalizedException
     */
    public function execute(Document $document, string $url, string $destinationPath): void
    {
        try {
            $this->saveImageFile->execute($document, $url, $destinationPath);
            $this->saveMediaGalleryAsset->execute($document, $destinationPath);
            $mediaAssetId = $this->getMediaGalleryAssetByPath->execute([$destinationPath])[0]->getId();

            $this->saveKeywords->execute(
                $mediaAssetId,
                $this->documentToKeywords->convert($document)
            );
            $this->saveAdobeStockAsset->execute(
                $this->documentToAsset->convert($document, ['media_gallery_id' => $mediaAssetId])
            );
        } catch (LocalizedException $localizedException) {
            throw $localizedException;
        } catch (\Exception $exception) {
            $this->logger->critical($exception);
            throw new CouldNotSaveException(__('Could not save image.'), $exception);
        }
    }
}
