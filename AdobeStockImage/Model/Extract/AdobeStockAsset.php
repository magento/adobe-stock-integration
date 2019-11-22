<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Magento\AdobeStockImage\Model\Extract;

use Magento\AdobeStockAssetApi\Api\Data\AssetInterface;
use Magento\AdobeStockAssetApi\Api\Data\AssetInterfaceFactory;
use Magento\AdobeStockAssetApi\Api\Data\CategoryInterfaceFactory;
use Magento\AdobeStockAssetApi\Api\Data\CreatorInterfaceFactory;
use Magento\Framework\Api\Search\Document;

/**
 * Adobe stock asset extractor
 */
class AdobeStockAsset
{
    private const DOCUMENT_FIELD_CATEGORY = 'category';
    private const DOCUMENT_CREATOR_FIELDS = ['creator_id' => 'id', 'creator_name' => 'name'];

    private const ASSET_FIELD_CATEGORY = 'category';
    private const ASSET_FIELD_CREATOR = 'creator';

    /**
     * @var AssetInterfaceFactory
     */
    private $assetFactory;

    /**
     * @var CreatorInterfaceFactory
     */
    private $creatorFactory;

    /**
     * @var CategoryInterfaceFactory
     */
    private $categoryFactory;

    /**
     * @param AssetInterfaceFactory $assetFactory
     * @param CreatorInterfaceFactory $creatorFactory
     * @param CategoryInterfaceFactory $categoryFactory
     */
    public function __construct(
        AssetInterfaceFactory $assetFactory,
        CreatorInterfaceFactory $creatorFactory,
        CategoryInterfaceFactory $categoryFactory
    ) {
        $this->assetFactory = $assetFactory;
        $this->creatorFactory = $creatorFactory;
        $this->categoryFactory = $categoryFactory;
    }

    /**
     * Convert search document to the asset object
     *
     * @param Document $document
     * @param array $additionalData
     * @return AssetInterface
     */
    public function convert(Document $document, array $additionalData = []): AssetInterface
    {
        $attributes = $document->getCustomAttributes();
        $assetData = [];
        $creatorData = [];
        $categoryData = [];
        foreach ($attributes as $attribute) {
            if ($attribute->getAttributeCode() === self::DOCUMENT_FIELD_CATEGORY) {
                $categoryData = $attribute->getValue();
                continue;
            }

            if (isset(self::DOCUMENT_CREATOR_FIELDS[$attribute->getAttributeCode()])) {
                $creatorData[self::DOCUMENT_CREATOR_FIELDS[$attribute->getAttributeCode()]] = $attribute->getValue();
                continue;
            }

            $assetData[$attribute->getAttributeCode()] = $attribute->getValue();
        }

        foreach ($additionalData as $key => $value) {
            $assetData[$key] = $value;
        }

        if (!empty($categoryData)) {
            $category = $this->categoryFactory->create(['data' => $categoryData]);
            $assetData[self::ASSET_FIELD_CATEGORY] = $category;
        }

        if (!empty($creatorData)) {
            $creator = $this->creatorFactory->create(['data' => $creatorData]);
            $assetData[self::ASSET_FIELD_CREATOR] = $creator;
        }

        return $this->assetFactory->create(['data' => $assetData]);
    }
}
