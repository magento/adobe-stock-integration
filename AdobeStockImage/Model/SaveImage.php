<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Magento\AdobeStockImage\Model;

use Magento\AdobeStockAssetApi\Api\SaveAssetInterface;
use Magento\AdobeStockImage\Model\Extract\AdobeStockAsset as DocumentToAsset;
use Magento\AdobeStockImageApi\Api\SaveImageInterface;
use Magento\Framework\Api\Search\Document;
use Magento\Framework\Exception\CouldNotSaveException;
use Magento\Framework\Exception\LocalizedException;
use Psr\Log\LoggerInterface;

/**
 * Save an image provided with the adobe Stock integration.
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
     * @var SaveImageFile
     */
    private $saveFile;

    /**
     * @var SaveMediaGalleryAsset
     */
    private $saveMediaGalleryAsset;

    /**
     * @var LoggerInterface
     */
    private $logger;

    /**
     * @param SaveAssetInterface $saveAdobeStockAsset
     * @param DocumentToAsset $documentToAsset
     * @param SaveImageFile $saveImageFile
     * @param SaveMediaGalleryAsset $saveMediaGalleryAsset
     * @param LoggerInterface $logger
     */
    public function __construct(
        SaveAssetInterface $saveAdobeStockAsset,
        DocumentToAsset $documentToAsset,
        SaveImageFile $saveImageFile,
        SaveMediaGalleryAsset $saveMediaGalleryAsset,
        LoggerInterface $logger
    ) {
        $this->saveAdobeStockAsset = $saveAdobeStockAsset;
        $this->documentToAsset = $documentToAsset;
        $this->saveFile = $saveImageFile;
        $this->saveMediaGalleryAsset = $saveMediaGalleryAsset;
        $this->logger = $logger;
    }

    /**
     * @inheritdoc
     */
    public function execute(Document $document, string $url, string $destinationPath): void
    {
        try {
            $this->saveFile->execute($document, $url, $destinationPath);
            $mediaAssetId = $this->saveMediaGalleryAsset->execute($document, $destinationPath);

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
