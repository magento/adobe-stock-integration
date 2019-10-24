<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Magento\AdobeStockImage\Model;

use Magento\AdobeMediaGalleryApi\Api\Data\AssetInterface;
use Magento\AdobeMediaGalleryApi\Model\Asset\Command\GetByIdInterface;
use Magento\AdobeMediaGalleryApi\Model\Asset\Command\SaveInterface;
use Magento\AdobeStockAsset\Model\DocumentToAsset;
use Magento\AdobeStockAsset\Model\DocumentToMediaGalleryAsset;
use Magento\AdobeStockAssetApi\Api\AssetRepositoryInterface;
use Magento\AdobeStockAssetApi\Api\SaveAssetInterface;
use Magento\AdobeStockImageApi\Api\SaveImageInterface;
use Magento\Framework\Api\Search\DocumentInterface;
use Magento\Framework\Exception\CouldNotSaveException;
use Magento\Framework\Exception\NoSuchEntityException;
use Psr\Log\LoggerInterface;
use Magento\AdobeMediaGallery\Model\Keyword\Command\SaveAssetKeywords;
use Magento\AdobeMediaGallery\Model\Keyword\Command\SaveAssetLinks;
use Magento\AdobeMediaGalleryApi\Model\Keyword\Command\GetAssetKeywordsInterface;

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
     * @var LoggerInterface
     */
    private $logger;

    /**
     * @var SaveAssetKeywords
     */
    private $saveAssetKeywords;

    /**
     * @var SaveAssetLinks
     */
    private $saveAssetLinks;

    /**
     * @var GetAssetKeywordsInterface
     */
    private $getAssetKeywords;

    /**
     * @param Storage $storage
     * @param GetByIdInterface $getMediaAssetById
     * @param AssetRepositoryInterface $assetRepository
     * @param SaveInterface $saveMediaAsset
     * @param SaveAssetInterface $saveAdobeStockAsset
     * @param DocumentToMediaGalleryAsset $documentToMediaGalleryAsset
     * @param DocumentToAsset $documentToAsset
     * @param LoggerInterface $logger
     * @param SaveAssetKeywords $saveAssetKeywords
     * @param SaveAssetLinks $saveAssetLinks
     * @param GetAssetKeywordsInterface $getAssetKeywords
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
        SaveAssetLinks $saveAssetLinks,
        GetAssetKeywordsInterface $getAssetKeywords
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
        $this->saveAssetLinks = $saveAssetLinks;
        $this->getAssetKeywords = $getAssetKeywords;
    }

    /**
     * @inheritdoc
     */
    public function execute(DocumentInterface $document, string $url, string $destinationPath): void
    {
        try {
            $pathAttribute = $document->getCustomAttribute('path');
            /* If the asset has been already saved, delete the previous version */
            if (!empty($pathAttribute)
                && !empty($pathAttribute->getValue())
            ) {
                $this->storage->delete($pathAttribute->getValue());
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

            $mediaGalleryAssetId = $mediaGalleryAssetId
                ?: $this->getExistingMediaGalleryAsset($document->getId())->getId();

            $keywords = $this->getAssetKeywords->execute($mediaGalleryAssetId);

            if (!empty($keywords)) {
                $keywordIds = $this->saveAssetKeywords->execute($keywords);
                $this->saveAssetLinks->execute($mediaGalleryAssetId, $keywordIds);
            }

            $asset = $this->documentToAsset->convert($document, ['media_gallery_id' => $mediaGalleryAssetId]);
            $this->saveAdobeStockAsset->execute($asset);
        } catch (\Exception $exception) {
            $message = __('Image was not saved: %1', $exception->getMessage());
            $this->logger->critical($message);
            throw new CouldNotSaveException($message);
        }
    }

    /**
     * Get media gallery asset if exists
     *
     * @param int $adobeStockAssetId
     * @return \Magento\AdobeMediaGalleryApi\Api\Data\AssetInterface
     * @throws NoSuchEntityException
     */
    private function getExistingMediaGalleryAsset(int $adobeStockAssetId): AssetInterface
    {
        $existingAdobeStockAsset = $this->assetRepository->getById($adobeStockAssetId);
        return $this->getMediaAssetById->execute($existingAdobeStockAsset->getMediaGalleryId());
    }
}
