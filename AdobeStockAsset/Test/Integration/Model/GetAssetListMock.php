<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Magento\AdobeStockAsset\Test\Integration\Model;

use Magento\AdobeStockAsset\Model\GetAssetList;
use Magento\Framework\Api\AttributeValue;
use Magento\Framework\Api\Search\Document;
use Magento\Framework\Api\Search\SearchCriteriaInterface;
use Magento\Framework\Api\Search\SearchResult;
use Magento\Framework\Api\Search\SearchResultInterface;

class GetAssetListMock extends GetAssetList
{
    /**
     * Search for images based on search criteria
     *
     * @param SearchCriteriaInterface $searchCriteria
     * @return SearchResultInterface
     */
    public function execute(SearchCriteriaInterface $searchCriteria): SearchResultInterface
    {
        $items = [
            new Document(
                [
                    'id' => 123455678,
                    'custom_attributes' => [
                        'id_field_name' => new AttributeValue(
                            ['attribute_code' => 'id_field_name']
                        )
                    ]
                ]
            )
        ];
        $searchResult = new SearchResult();
        $searchResult->setSearchCriteria($searchCriteria);
        $searchResult->setItems($items);
        $searchResult->setTotalCount(1);

        return $searchResult;
    }
}
