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
use Magento\MediaGalleryApi\Model\Keyword\Command\SaveAssetKeywordsInterface;
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
     * @var SaveAssetKeywordsInterface
     */
    private $saveAssetKeywords;

    /**
     * @var SetLicensedInMediaGalleryGrid
     */
    private $setLicensedInMediaGalleryGrid;

    /**
     * @var RetrieveFilePathFromDocumentInterface
     */
    private $retrieveFilePathFromDocument;

    /**
     * @var RetrieveMediaAssetIdFromDocumentInterface
     */
    private $retrieveMediaAssetIdFromDocument;

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
     * @param SetLicensedInMediaGalleryGrid $setLicensedInMediaGalleryGrid
     * @param RetrieveFilePathFromDocumentInterface $retrieveFilePathFromDocument
     * @param RetrieveMediaAssetIdFromDocumentInterface $retrieveMediaAssetIdFromDocument
     * @param LoggerInterface $logger
     */
    public function __construct(
        SaveAssetInterface $saveAdobeStockAsset,
        DocumentToAsset $documentToAsset,
        SaveAssetKeywordsInterface $saveAssetKeywords,
        DocumentToKeywords $documentToKeywords,
        SetLicensedInMediaGalleryGrid $setLicensedInMediaGalleryGrid,
        RetrieveFilePathFromDocumentInterface $retrieveFilePathFromDocument,
        RetrieveMediaAssetIdFromDocumentInterface $retrieveMediaAssetIdFromDocument,
        LoggerInterface $logger
    ) {
        $this->saveAdobeStockAsset = $saveAdobeStockAsset;
        $this->documentToAsset = $documentToAsset;
        $this->saveAssetKeywords = $saveAssetKeywords;
        $this->documentToKeywords = $documentToKeywords;
        $this->setLicensedInMediaGalleryGrid = $setLicensedInMediaGalleryGrid;
        $this->retrieveFilePathFromDocument = $retrieveFilePathFromDocument;
        $this->retrieveMediaAssetIdFromDocument = $retrieveMediaAssetIdFromDocument;
        $this->logger = $logger;
    }

    /**
     * Downloads the image and save it to file system database
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
            $filePath = $this->retrieveFilePathFromDocument->execute($document, $url, $destinationPath);
            $mediaGalleryAssetId = $this->retrieveMediaAssetIdFromDocument->execute($document, $filePath);

            $keywords = $this->documentToKeywords->convert($document);
            $this->saveAssetKeywords->execute($keywords, $mediaGalleryAssetId);

            $asset = $this->documentToAsset->convert($document, ['media_gallery_id' => $mediaGalleryAssetId]);
            $this->saveAdobeStockAsset->execute($asset);
            $this->setLicensedInMediaGalleryGrid->execute($asset);
        } catch (\Exception $exception) {
            $this->logger->critical($exception);
            $message = __('An error occurred during save image.');
            throw new CouldNotSaveException($message);
        }
    }
}
