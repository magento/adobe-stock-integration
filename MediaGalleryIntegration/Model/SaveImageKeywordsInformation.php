<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Magento\MediaGalleryIntegration\Model;

use Magento\MediaGalleryMetadataApi\Api\ExtractMetadataInterface;
use Magento\MediaGalleryApi\Api\Data\AssetKeywordsInterfaceFactory;
use Magento\MediaGalleryApi\Api\SaveAssetsKeywordsInterface;
use Magento\MediaGalleryApi\Api\GetAssetsByPathsInterface;
use Magento\MediaGallerySynchronization\Model\CreateAssetFromFile;
use Magento\MediaGalleryApi\Api\Data\KeywordInterfaceFactory;

/**
 * Save image keywords metadata to the database
 */
class SaveImageKeywordsInformation
{
    /**
     * @var KeywordInterfaceFactory
     */
    private $keywordFactory;

    /**
     * @var AssetKeywordsInterfaceFactory
     */
    private $assetKeywordsFactory;
    
    /**
     * @var ExtractMetadataInterface
     */
    private $extractMetadata;

    /**
     * @var SaveKeywords
     */
    private $saveAssetKeywords;

    /**
     * @var GetAssetsByPathsInterface
     */
    private $getMediaGalleryAssetByPath;

    /**
     * @var CreateAssetFromFile
     */
    private $createAssetFromFile;

    /**
     * @param KeywordInterfaceFactory $keywordFactory
     * @param ExtractMetadataInterface $extractMetadata
     * @param SaveAssetsKeywordsInterface $saveAssetKeywords
     * @param AssetKeywordsInterfaceFactory $assetKeywordsFactory
     * @param GetAssetsByPathsInterface $getMediaGalleryAssetByPath
     * @param CreateAssetFromFile $createAssetFromFile
     */
    public function __construct(
        KeywordInterfaceFactory $keywordFactory,
        ExtractMetadataInterface $extractMetadata,
        SaveAssetsKeywordsInterface $saveAssetKeywords,
        AssetKeywordsInterfaceFactory $assetKeywordsFactory,
        GetAssetsByPathsInterface $getMediaGalleryAssetByPath,
        CreateAssetFromFile $createAssetFromFile
    ) {
        $this->createAssetFromFile = $createAssetFromFile;
        $this->keywordFactory = $keywordFactory;
        $this->extractMetadata = $extractMetadata;
        $this->saveAssetKeywords = $saveAssetKeywords;
        $this->assetKeywordsFactory = $assetKeywordsFactory;
        $this->getMediaGalleryAssetByPath = $getMediaGalleryAssetByPath;
    }

    /**
     * Save image keywords metadata to the database.
     *
     * @param \SplFileInfo $file
     */
    public function execute(\SplFileInfo $file): void
    {
        $keywords = [];
        $asset = $this->createAssetFromFile->execute($file);
        $metadata = $this->extractMetadata->execute($file->getPath() . '/' . $file->getFileName());

        foreach ($metadata->getKeywords() as $keyword) {
            $keywords[] = $this->keywordFactory->create(
                [
                    'keyword' => $keyword
                ]
            );
        }
        
        $mediaAssetId = $this->getMediaGalleryAssetByPath->execute([$asset->getPath()])[0]->getId();
        
        $assetKeywords = $this->assetKeywordsFactory->create([
            'assetId' => $mediaAssetId,
            'keywords' => $keywords
        ]);
        
        $this->saveAssetKeywords->execute([$assetKeywords]);
    }
}
