<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Magento\AdobeStockAsset\Model;

use Magento\AdobeMediaGalleryApi\Api\Data\AssetInterface;
use Magento\Framework\Api\Search\DocumentInterface;
use Magento\AdobeMediaGalleryApi\Api\Data\AssetInterfaceFactory;
use Magento\AdobeMediaGalleryApi\Api\Data\KeywordInterfaceFactory;

/**
 * Class DocumentToMediaGalleryAsset
 */
class DocumentToMediaGalleryAsset
{
    private const DOCUMENT_FIELD_KEYWORDS = 'keywords';
    private const DOCUMENT_FIELD_KEYWORD_NAME = 'name';

    private const ASSET_FIELD_KEYWORDS = 'keywords';
    private const ASSET_FIELD_KEYWORD_NAME = 'keyword';

    /**
     * @var AssetInterfaceFactory
     */
    private $assetFactory;

    /**
     * @var KeywordInterfaceFactory
     */
    private $keywordFactory;

    /**
     * @param AssetInterfaceFactory $assetFactory
     * @param KeywordInterfaceFactory $keywordFactory
     */
    public function __construct(
        AssetInterfaceFactory $assetFactory,
        KeywordInterfaceFactory $keywordFactory
    ) {
        $this->assetFactory = $assetFactory;
        $this->keywordFactory = $keywordFactory;
    }

    /**
     * Convert search document to the asset object
     *
     * @param DocumentInterface $document
     * @param array $additionalData
     * @return AssetInterface
     */
    public function convert(DocumentInterface $document, array $additionalData = []): AssetInterface
    {
        $attributes = $document->getCustomAttributes();
        $assetData = [];
        $keywordsData = [];
        foreach ($attributes as $attribute) {
            if ($attribute->getAttributeCode() === self::DOCUMENT_FIELD_KEYWORDS) {
                foreach ($attribute->getValue() as $keywordData) {
                    $keywordsData[] = [
                        self::ASSET_FIELD_KEYWORD_NAME => $keywordData[self::DOCUMENT_FIELD_KEYWORD_NAME]
                    ];
                }
                continue;
            }

            $assetData[$attribute->getAttributeCode()] = $attribute->getValue();
        }

        foreach ($additionalData as $key => $value) {
            $assetData[$key] = $value;
        }

        $keywords = [];
        foreach ($keywordsData as $keywordData) {
            $keywords[] = $this->keywordFactory->create(['data' => $keywordData]);
        }

        $assetData[self::ASSET_FIELD_KEYWORDS] = $keywords;

        return $this->assetFactory->create(['data' => $assetData]);
    }
}
