<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

declare(strict_types=1);

namespace Magento\AdobeStockAsset\Model;

use Magento\Framework\Api\AttributeValueFactory;
use Magento\Framework\Api\Search\SearchResultInterface;
use Magento\Framework\App\ResourceConnection;

/**
 * Class AddIsDownloadedToSearchResult
 */
class AddIsDownloadedToSearchResult
{
    const TABLE_ASSET                  = 'adobe_stock_asset';
    const ATTRIBUTE_CODE_IS_DOWNLOADED = 'is_downloaded';

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
                ->from(self::TABLE_ASSET, ['id'])
                ->where('id in (?)', $itemIds);
            $downloadedIds = $connection->fetchCol($select, 'id');

            foreach ($items as $key => $item) {
                $customAttributes = $item->getCustomAttributes();

                $attribute = $this->attributeValueFactory->create();
                $attribute->setAttributeCode(self::ATTRIBUTE_CODE_IS_DOWNLOADED);
                $attribute->setValue((int)in_array($item->getId(), $downloadedIds));

                $customAttributes[self::ATTRIBUTE_CODE_IS_DOWNLOADED] = $attribute;
                $item->setCustomAttributes($customAttributes);
            }
        }

        return $searchResult;
    }
}
