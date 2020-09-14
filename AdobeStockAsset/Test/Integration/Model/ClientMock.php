<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Magento\AdobeStockAsset\Test\Integration\Model;

use Magento\AdobeStockClient\Model\Client;
use Magento\Framework\Api\AttributeValue;
use Magento\Framework\Api\Search\Document;
use Magento\Framework\Api\Search\SearchCriteriaInterface;
use Magento\Framework\Api\Search\SearchResult;
use Magento\Framework\Api\Search\SearchResultInterface;

class ClientMock extends Client
{
    private const ID = 'id';
    private const CUSTOM_ATTRIBUTES = 'custom_attributes';

    /**
     * Search for assets
     *
     * @param SearchCriteriaInterface $searchCriteria
     * @return SearchResultInterface
     */
    public function search(SearchCriteriaInterface $searchCriteria): SearchResultInterface
    {
        $items = [];
        foreach ($this->getStockFiles() as $file) {
            $items[] = $this->getStockFileDocument($file);
        }

        $searchResult = new SearchResult();
        $searchResult->setSearchCriteria($searchCriteria);
        $searchResult->setItems($items);
        $searchResult->setTotalCount(1);

        return $searchResult;
    }

    /**
     * Get array of stock files data.
     *
     * @return array
     */
    private function getStockFiles(): array
    {
        $stockFilesData = [
            [
                'id' => 1,
                'custom_attributes' => [
                    'id_field_name' => 'id',
                    'id' => 1,
                    'thumbnail_240_url' => 'https://test.url/1',
                    'width' => 110,
                    'height' => 210,
                    'comp_url' => 'https://test.url/1',
                    'category' => [
                        'id' => 1,
                        'name' => 'Test',
                        'link' => null
                    ],
                    'category_id' => 1
                ]
            ]
        ];

        return $stockFilesData;
    }

    /**
     * @param array $stockFiles
     * @return Document
     */
    private function getStockFileDocument(array $stockFiles): Document
    {
        $item = new Document();
        $item->setId($stockFiles[self::ID]);

        $attributes = [];
        foreach ($stockFiles[self::CUSTOM_ATTRIBUTES] as $attributeCode => $value) {
            $attribute = new AttributeValue();
            $attribute->setAttributeCode($attributeCode);
            $attribute->setValue($value);
            $attributes[$attributeCode] = $attribute;
        }

        $item->setCustomAttributes($attributes);

        return $item;
    }
}
