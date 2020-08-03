<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Magento\AdobeStockAsset\Test\Api;

use Magento\Framework\Api\FilterBuilder;
use Magento\Framework\Api\Search\FilterGroupBuilder;
use Magento\Framework\Api\Search\SearchCriteriaBuilder;
use Magento\Framework\Api\SortOrder;
use Magento\Framework\Api\SortOrderBuilder;
use Magento\Framework\Webapi\Rest\Request;
use Magento\TestFramework\Helper\Bootstrap;
use Magento\TestFramework\TestCase\WebapiAbstract;

/**
 * Test searching in Adobe Stock service though the WebAPI.
 */
class SearchAdobeStockTest extends WebapiAbstract
{
    private const RESOURCE_PATH = '/V1/adobestock/asset/list';
    private const SERVICE_NAME = 'adobeStockAssetApiGetAssetListV1';
    private const REQUEST_NAME = 'search_adobe_stock_content';

    /**
     * Test get list WEB API method.
     *
     * @return void
     */
    public function testGetList(): void
    {
        /** @var SearchCriteriaBuilder $searchCriteriaBuilder */
        $searchCriteriaBuilder = Bootstrap::getObjectManager()->create(SearchCriteriaBuilder::class);

        $searchCriteriaBuilder->setPageSize(32);
        $searchCriteriaBuilder->setCurrentPage(1);

        /** @var FilterBuilder $filterBuilder */
        $filterBuilder = Bootstrap::getObjectManager()->create(FilterBuilder::class);
        $wordsFilter = $filterBuilder->setField('words')
            ->setValue('car')
            ->setConditionType('fulltext')
            ->create();

        $illustrationFilter = $filterBuilder->setField('content_type_filter')
            ->setValue('illustration')
            ->setConditionType('eq')
            ->create();

        $photoFilter = $filterBuilder->setField('content_type_filter')
            ->setValue('photo')
            ->setConditionType('eq')
            ->create();

        $premiumPriceFilter = $filterBuilder->setField('premium_price_filter')
            ->setValue('ALL')
            ->setConditionType('eq')
            ->create();

        /** @var FilterGroupBuilder $filterGroupBuilder */
        $filterGroupBuilder = Bootstrap::getObjectManager()->create(FilterGroupBuilder::class);
        $wordsFilterGroup = $filterGroupBuilder->setFilters([$wordsFilter])->create();
        $contentTypeFilterGroup = $filterGroupBuilder->setFilters([$illustrationFilter, $photoFilter])->create();
        $premiumPriceFilterGroup = $filterGroupBuilder->setFilters([$premiumPriceFilter])->create();

        /** @var SortOrderBuilder $sortOrderBuilder */
        $sortOrderBuilder = Bootstrap::getObjectManager()->create(SortOrderBuilder::class);
        $sortOrder = $sortOrderBuilder->setField('id')
            ->setDirection(SortOrder::SORT_DESC)
            ->create();

        $searchCriteria = $searchCriteriaBuilder->create();
        $searchCriteria->setSortOrders([$sortOrder]);
        $searchCriteria->setRequestName(self::REQUEST_NAME);
        $searchCriteria->setFilterGroups([$wordsFilterGroup, $contentTypeFilterGroup, $premiumPriceFilterGroup]);

        $requestData = ['search_criteria' => $searchCriteria->__toArray()];

        $serviceInfo = [
            'rest' => [
                'resourcePath' => self::RESOURCE_PATH . '?' . http_build_query($requestData),
                'httpMethod' => Request::HTTP_METHOD_GET,
            ],
            'soap' => [
                'service' => self::SERVICE_NAME,
                'operation' => self::SERVICE_NAME . 'GetList',
            ],
        ];

        $response = $this->_webApiCall($serviceInfo, $searchCriteria);

        $this->assertArrayHasKey('search_criteria', $response);
        $this->assertArrayHasKey('total_count', $response);
        $this->assertArrayHasKey('items', $response);

        $this->assertEquals($requestData['search_criteria'], $response['search_criteria']);
        $this->assertTrue($response['total_count'] > 0);
        $this->assertTrue(count($response['items']) > 0);
    }
}
