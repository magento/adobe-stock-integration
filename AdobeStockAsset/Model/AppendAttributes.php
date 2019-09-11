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

                //adding is_downloaded attribute
                $isDownloadedValue = (int)isset($downloadedAssets[$item->getId()]);
                $attribute = $this->attributeValueFactory->create();
                $attribute->setAttributeCode(self::ATTRIBUTE_CODE_IS_DOWNLOADED);
                $attribute->setValue($isDownloadedValue);
                $customAttributes[self::ATTRIBUTE_CODE_IS_DOWNLOADED] = $attribute;

                //adding path attribute
                $pathValue = $downloadedAssets[$item->getId()][AssetInterface::PATH] ?? '';
                $attribute = $this->attributeValueFactory->create();
                $attribute->setAttributeCode(self::ATTRIBUTE_CODE_PATH);
                $attribute->setValue($pathValue);
                $customAttributes[self::ATTRIBUTE_CODE_PATH] = $attribute;

                $item->setCustomAttributes($customAttributes);
            }
        }

        return $searchResult;
    }
}
