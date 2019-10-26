<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Magento\AdobeStockImage\Model;

use Magento\AdobeMediaGalleryApi\Model\Asset\Command\GetByIdInterface;
use Magento\AdobeMediaGalleryApi\Model\Asset\Command\SaveInterface;
use Magento\AdobeStockAsset\Model\Extract\AdobeStockAsset as DocumentToAsset;
use Magento\AdobeStockAsset\Model\Extract\MediaGalleryAsset as DocumentToMediaGalleryAsset;
use Magento\AdobeStockAsset\Model\Extract\Keywords as DocumentToKeywords;
use Magento\AdobeStockAssetApi\Api\AssetRepositoryInterface;
use Magento\AdobeStockAssetApi\Api\SaveAssetInterface;
use Magento\AdobeStockImageApi\Api\SaveImageInterface;
use Magento\Framework\Api\Search\DocumentInterface;
use Magento\Framework\Exception\CouldNotSaveException;
use Psr\Log\LoggerInterface;
use Magento\AdobeMediaGallery\Model\Keyword\Command\SaveAssetKeywords;

/**
 * Class SaveImage
 */
class SaveImage implements SaveImageInterface
{
    /**
     * @var Storage
     */
    private $storage;

    /**
     * @var GetByIdInterface
     */
    private $getMediaAssetById;

    /**
     * @var AssetRepositoryInterface
     */
    private $assetRepository;

    /**
     * @var SaveInterface
     */
    private $saveMediaAsset;

    /**
     * @var SaveAssetInterface
     */
    private $saveAdobeStockAsset;

    /**
     * @var DocumentToMediaGalleryAsset
     */
    private $documentToMediaGalleryAsset;

    /**
     * @var DocumentToAsset
     */
    private $documentToAsset;

    /**
     * @var DocumentToKeywords
     */
    private $documentToKeywords;

    /**
     * @var LoggerInterface
     */
    private $logger;

    /**
     * @var SaveAssetKeywords
     */
    private $saveAssetKeywords;

    /**
     * SaveImage constructor.
     *
     * @param Storage $storage
     * @param GetByIdInterface $getMediaAssetById
     * @param AssetRepositoryInterface $assetRepository
     * @param SaveInterface $saveMediaAsset
     * @param SaveAssetInterface $saveAdobeStockAsset
     * @param DocumentToMediaGalleryAsset $documentToMediaGalleryAsset
     * @param DocumentToAsset $documentToAsset
     * @param LoggerInterface $logger
     * @param SaveAssetKeywords $saveAssetKeywords
     * @param DocumentToKeywords $documentToKeywords
     */
    public function __construct(
        Storage $storage,
        GetByIdInterface $getMediaAssetById,
        AssetRepositoryInterface $assetRepository,
        SaveInterface $saveMediaAsset,
        SaveAssetInterface $saveAdobeStockAsset,
        DocumentToMediaGalleryAsset $documentToMediaGalleryAsset,
        DocumentToAsset $documentToAsset,
        LoggerInterface $logger,
        SaveAssetKeywords $saveAssetKeywords,
        DocumentToKeywords $documentToKeywords
    ) {
        $this->storage = $storage;
        $this->getMediaAssetById = $getMediaAssetById;
        $this->assetRepository = $assetRepository;
        $this->saveMediaAsset = $saveMediaAsset;
        $this->saveAdobeStockAsset = $saveAdobeStockAsset;
        $this->documentToMediaGalleryAsset = $documentToMediaGalleryAsset;
        $this->documentToAsset = $documentToAsset;
        $this->logger = $logger;
        $this->saveAssetKeywords = $saveAssetKeywords;
        $this->documentToKeywords = $documentToKeywords;
    }

    /**
     * @inheritdoc
     */
    public function execute(DocumentInterface $document, string $url, string $destinationPath): void
    {
        try {
            $pathAttribute = $document->getCustomAttribute('path');
            $pathValue = $pathAttribute->getValue();
            /* If the asset has been already saved, delete the previous version */
            if (null !== $pathAttribute && $pathValue) {
                $this->storage->delete($pathValue);
            }

            $path = $this->storage->save($url, $destinationPath);

            $mediaGalleryAsset = $this->documentToMediaGalleryAsset->convert(
                $document,
                [
                    'id' => null,
                    'path' => $path,
                    'source' => 'Adobe Stock'
                ]
            );
            $mediaGalleryAssetId = $this->saveMediaAsset->execute($mediaGalleryAsset);

            if (!$mediaGalleryAssetId) {
                $mediaGalleryAssetId = $this->assetRepository->getById($document->getId())->getMediaGalleryId();
            }

            $this->saveAssetKeywords->execute($this->documentToKeywords->convert($document), $mediaGalleryAssetId);

            $asset = $this->documentToAsset->convert($document, ['media_gallery_id' => $mediaGalleryAssetId]);
            $this->saveAdobeStockAsset->execute($asset);
        } catch (\Exception $exception) {
            $message = __('Image was not saved: %1', $exception->getMessage());
            $this->logger->critical($message);
            throw new CouldNotSaveException($message);
        }
    }
}
