<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Magento\MediaGalleryUi\Model\ProcessImageDetails;

use Magento\MediaGalleryApi\Api\Data\AssetKeywordsInterfaceFactory;
use Magento\MediaGalleryApi\Api\Data\KeywordInterfaceFactory;
use Magento\MediaGalleryApi\Api\SaveAssetsKeywordsInterface;

class ProcessKeywords
{
    /**
     * @var AssetKeywordsInterfaceFactory
     */
    private $assetKeywordsFactory;

    /**
     * @var KeywordInterfaceFactory
     */
    private $keywordFactory;

    /**
     * @var SaveAssetsKeywordsInterface
     */
    private $saveAssetKeywords;

    /**
     * @param AssetKeywordsInterfaceFactory $assetKeywordsFactory
     * @param KeywordInterfaceFactory $keywordFactory
     * @param SaveAssetsKeywordsInterface $saveAssetKeywords
     */
    public function __construct(
        AssetKeywordsInterfaceFactory $assetKeywordsFactory,
        KeywordInterfaceFactory $keywordFactory,
        SaveAssetsKeywordsInterface $saveAssetKeywords
    ) {
        $this->assetKeywordsFactory = $assetKeywordsFactory;
        $this->keywordFactory = $keywordFactory;
        $this->saveAssetKeywords = $saveAssetKeywords;
    }

    /**
     * Save asset keywords
     *
     * @param array $imageKeywords
     * @param int $imageId
     */
    public function execute(array $imageKeywords, int $imageId): void
    {
        $arrayKeywords = $this->convertKeywords($imageKeywords);
        $assetKeywords = $this->assetKeywordsFactory->create([
            'assetId' => $imageId,
            'keywords' => $arrayKeywords
        ]);

        $this->saveAssetKeywords->execute([$assetKeywords]);
    }

    /**
     * Convert keywords
     *
     * @param array $keywords
     * @return array
     */
    private function convertKeywords(array $keywords): array
    {
        $arrayKeywords = [];
        foreach ($keywords as $keyword) {
            $arrayKeywords[] = $this->keywordFactory->create(
                [
                    'keyword' => $keyword
                ]
            );
        }
        return $arrayKeywords;
    }
}
