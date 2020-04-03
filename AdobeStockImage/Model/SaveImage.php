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
use Magento\Framework\Exception\AlreadyExistsException;
use Magento\Framework\Exception\CouldNotDeleteException;
use Magento\Framework\Exception\CouldNotSaveException;
use Magento\Framework\Exception\FileSystemException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\MediaGalleryApi\Model\Keyword\Command\SaveAssetKeywordsInterface;

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
     * @var SaveImageFile
     */
    private $saveImageFile;

    /**
     * @var SaveMediaGalleryAsset
     */
    private $saveMediaGalleryAsset;

    /**
     * SaveImage constructor.
     *
     * @param SaveAssetInterface $saveAdobeStockAsset
     * @param DocumentToAsset $documentToAsset
     * @param SaveAssetKeywordsInterface $saveAssetKeywords
     * @param DocumentToKeywords $documentToKeywords
     * @param SetLicensedInMediaGalleryGrid $setLicensedInMediaGalleryGrid
     * @param SaveImageFileInterface $saveImageFile
     * @param SaveMediaGalleryAssetInterface $saveMediaGalleryAsset
     */
    public function __construct(
        SaveAssetInterface $saveAdobeStockAsset,
        DocumentToAsset $documentToAsset,
        SaveAssetKeywordsInterface $saveAssetKeywords,
        DocumentToKeywords $documentToKeywords,
        SetLicensedInMediaGalleryGrid $setLicensedInMediaGalleryGrid,
        SaveImageFileInterface $saveImageFile,
        SaveMediaGalleryAssetInterface $saveMediaGalleryAsset
    ) {
        $this->saveAdobeStockAsset = $saveAdobeStockAsset;
        $this->documentToAsset = $documentToAsset;
        $this->saveAssetKeywords = $saveAssetKeywords;
        $this->documentToKeywords = $documentToKeywords;
        $this->setLicensedInMediaGalleryGrid = $setLicensedInMediaGalleryGrid;
        $this->saveImageFile = $saveImageFile;
        $this->saveMediaGalleryAsset = $saveMediaGalleryAsset;
    }

    /**
     * Downloads the image and save it to file system database
     *
     * @param Document $document
     * @param string $url
     * @param string $destinationPath
     *
     * @throws AlreadyExistsException
     * @throws CouldNotDeleteException
     * @throws CouldNotSaveException
     * @throws FileSystemException
     * @throws NoSuchEntityException
     */
    public function execute(Document $document, string $url, string $destinationPath): void
    {
        $filePath = $this->saveImageFile->execute($document, $url, $destinationPath);
        $mediaGalleryAssetId = $this->saveMediaGalleryAsset->execute($document, $filePath);

        $keywords = $this->documentToKeywords->convert($document);
        $this->saveAssetKeywords->execute($keywords, $mediaGalleryAssetId);

        $asset = $this->documentToAsset->convert($document, ['media_gallery_id' => $mediaGalleryAssetId]);
        $this->saveAdobeStockAsset->execute($asset);
        $this->setLicensedInMediaGalleryGrid->execute($asset);
    }
}
