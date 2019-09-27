<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

declare(strict_types=1);

namespace Magento\AdobeStockAsset\Model;

use Magento\AdobeStockAssetApi\Api\Data\AssetInterface;
use Magento\Framework\Api\AttributeValueFactory;
use Magento\Framework\Api\Search\SearchResultInterface;
use Magento\Framework\App\ResourceConnection;

/**
 * Class AddIsDownloadedToSearchResult
 */
class AppendAttributes
{
    const TABLE_ASSET                  = 'adobe_stock_asset';
    const ATTRIBUTE_CODE_IS_DOWNLOADED = 'is_downloaded';
    const ATTRIBUTE_CODE_PATH          = 'path';

    /**
     * @var AttributeValueFactory
     */
    private $attributeValueFactory;

    /**
     * @var ResourceConnection
     */
    private $resourceConnection;

    /**
     * AddIsDownloadedToSearchResult constructor.
     * @param ResourceConnection $resourceConnection
     * @param AttributeValueFactory $attributeValueFactory
     */
    public function __construct(
        ResourceConnection $resourceConnection,
        AttributeValueFactory $attributeValueFactory
    ) {
        $this->resourceConnection = $resourceConnection;
        $this->attributeValueFactory = $attributeValueFactory;
    }

    /**
     * Add additional asset attributes
     *
     * @param SearchResultInterface $searchResult
     * @return SearchResultInterface
     */
    public function execute(SearchResultInterface $searchResult): SearchResultInterface
    {
        $items = $searchResult->getItems();
        $itemIds = [];

        foreach ($items as $key => $item) {
            $itemIds[$key] = $item->getId();
        }

        if (count($itemIds)) {
            $connection = $this->resourceConnection->getConnection();
            $select = $connection->select()
                ->from(self::TABLE_ASSET)
                ->where('id in (?)', $itemIds);
            $downloadedAssets = $connection->fetchAssoc($select);

            foreach ($items as $key => $item) {
                $customAttributes = $item->getCustomAttributes();

                if (isset($downloadedAssets[$item->getId()])) {
                    $assetData = $downloadedAssets[$item->getId()];
                    $this->addIsDownloaded($customAttributes, $assetData);
                    $this->addPath($customAttributes, $assetData);
                }

                $item->setCustomAttributes($customAttributes);
            }
        }

        return $searchResult;
    }

    /**
     * Add is_downloaded attribute
     *
     * @param array|null $customAttributes
     * @param array|null $assetData
     */
    private function addIsDownloaded(?array &$customAttributes, ?array $assetData)
    {
        $isDownloadedValue = (int)isset($assetData[AssetInterface::ID]);
        $attribute = $this->attributeValueFactory->create();
        $attribute->setAttributeCode(self::ATTRIBUTE_CODE_IS_DOWNLOADED);
        $attribute->setValue($isDownloadedValue);
        $customAttributes[self::ATTRIBUTE_CODE_IS_DOWNLOADED] = $attribute;
    }

    /**
     * Add path attribute
     *
     * @param array|null $customAttributes
     * @param array|null $assetData
     */
    private function addPath(?array &$customAttributes, ?array $assetData)
    {
        $pathValue = $assetData[AssetInterface::PATH] ?? '';
        $attribute = $this->attributeValueFactory->create();
        $attribute->setAttributeCode(self::ATTRIBUTE_CODE_PATH);
        $attribute->setValue($pathValue);
        $customAttributes[self::ATTRIBUTE_CODE_PATH] = $attribute;
    }
}
