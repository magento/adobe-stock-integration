<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Magento\AdobeStockImage\Model;

use Magento\AdobeStockImage\Model\Extract\MediaGalleryAsset as DocumentToAsset;
use Magento\Framework\Api\Search\Document;
use Magento\Framework\Exception\CouldNotSaveException;
use Magento\Framework\Exception\LocalizedException;
use Magento\MediaGalleryApi\Api\Data\AssetInterface;
use Magento\MediaGalleryApi\Api\GetAssetsByPathsInterface;
use Magento\MediaGalleryApi\Api\SaveAssetsInterface;
use Magento\MediaGallerySynchronizationApi\Api\SynchronizeFilesInterface;

/**
 * Process save action of the media gallery asset and keywords.
 */
class SaveMediaGalleryAsset
{
    private const SOURCE_ADOBE_STOCK = 'Adobe Stock';

    /**
     * @var DocumentToAsset
     */
    private $documentToAsset;

    /**
     * @var GetAssetsByPathsInterface
     */
    private $getAssetsByPaths;

    /**
     * @var SynchronizeFilesInterface
     */
    private $importFiles;

    /**
     * @var SaveAssetsInterface
     */
    private $saveAssets;

    /**
     * @var SaveKeywords
     */
    private $saveKeywords;

    /**
     * @param DocumentToAsset $documentToAsset
     * @param GetAssetsByPathsInterface $getAssetsByPaths
     * @param SynchronizeFilesInterface $importFiles
     * @param SaveAssetsInterface $saveAssets
     * @param SaveKeywords $saveKeywords
     */
    public function __construct(
        DocumentToAsset $documentToAsset,
        GetAssetsByPathsInterface $getAssetsByPaths,
        SynchronizeFilesInterface $importFiles,
        SaveAssetsInterface $saveAssets,
        SaveKeywords $saveKeywords
    ) {
        $this->documentToAsset = $documentToAsset;
        $this->getAssetsByPaths = $getAssetsByPaths;
        $this->importFiles = $importFiles;
        $this->saveAssets = $saveAssets;
        $this->saveKeywords = $saveKeywords;
    }

    /**
     * Process saving MediaGalleryAsset based on the search document and destination path.
     *
     * @param Document $document
     * @param string $path
     * @return int
     * @throws CouldNotSaveException
     * @throws LocalizedException
     */
    public function execute(Document $document, string $path): int
    {
        try {
            $this->importFiles->execute([$path]);
            $asset = $this->getAssetsByPaths->execute([$path])[0];
            $this->saveAsset($document, $asset);
            $this->saveKeywords($document, $asset->getId());
            return $asset->getId();
        } catch (LocalizedException $exception) {
            throw $exception;
        } catch (\Exception $exception) {
            throw new CouldNotSaveException(__('Could not save media gallery asset.'), $exception);
        }
    }

    /**
     * Save media gallery asset
     *
     * @param Document $document
     * @param AssetInterface $asset
     * @throws CouldNotSaveException
     */
    private function saveAsset(Document $document, AssetInterface $asset): void
    {
        $this->saveAssets->execute(
            [
                $this->documentToAsset->convert(
                    $document,
                    [
                        'id' => $asset->getId(),
                        'path' => $asset->getPath(),
                        'source' => self::SOURCE_ADOBE_STOCK,
                        'description' => $asset->getDescription(),
                        'hash' => $asset->getHash(),
                        'width' => $asset->getWidth(),
                        'height' => $asset->getHeight(),
                        'size' => $asset->getSize()
                    ]
                )
            ]
        );
    }

    /**
     * Save media gallery asset keywords
     *
     * @param Document $document
     * @param int $id
     * @throws CouldNotSaveException
     * @throws LocalizedException
     */
    private function saveKeywords(Document $document, int $id): void
    {
        $this->saveKeywords->execute($id, $document);
    }
}
