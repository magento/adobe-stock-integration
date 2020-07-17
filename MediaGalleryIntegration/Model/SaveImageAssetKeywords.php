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
use Magento\MediaGalleryApi\Api\Data\KeywordInterfaceFactory;

/**
 * Save image keywords metadata to the database
 */
class SaveImageAssetKeywords
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
     * @param KeywordInterfaceFactory $keywordFactory
     * @param ExtractMetadataInterface $extractMetadata
     * @param SaveAssetsKeywordsInterface $saveAssetKeywords
     * @param AssetKeywordsInterfaceFactory $assetKeywordsFactory
     */
    public function __construct(
        KeywordInterfaceFactory $keywordFactory,
        ExtractMetadataInterface $extractMetadata,
        SaveAssetsKeywordsInterface $saveAssetKeywords,
        AssetKeywordsInterfaceFactory $assetKeywordsFactory
    ) {
        $this->keywordFactory = $keywordFactory;
        $this->extractMetadata = $extractMetadata;
        $this->saveAssetKeywords = $saveAssetKeywords;
        $this->assetKeywordsFactory = $assetKeywordsFactory;
    }

    /**
     * Save image keywords metadata to the database.
     *
     * @param string $filePath
     * @params int $mediaAssetId
     */
    public function execute(string $filePath, int $mediaAssetId): void
    {
        $keywords = [];
        $metadata = $this->extractMetadata->execute($filePath);

        foreach ($metadata->getKeywords() as $keyword) {
            $keywords[] = $this->keywordFactory->create(
                [
                    'keyword' => $keyword
                ]
            );
        }
        
        $assetKeywords = $this->assetKeywordsFactory->create([
            'assetId' => $mediaAssetId,
            'keywords' => $keywords
        ]);
        
        $this->saveAssetKeywords->execute([$assetKeywords]);
    }
}
