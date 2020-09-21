<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Magento\AdobeStockAsset\Test\Api\AssetRepository;

use Magento\Framework\Api\FilterBuilder;
use Magento\Framework\Api\Search\FilterGroupBuilder;
use Magento\Framework\Api\Search\SearchCriteriaBuilder;
use Magento\Framework\Api\SortOrder;
use Magento\Framework\Api\SortOrderBuilder;
use Magento\Framework\Webapi\Rest\Request;
use Magento\TestFramework\Helper\Bootstrap;
use Magento\TestFramework\TestCase\WebapiAbstract;

/**
 * Testing searching for the Adobe Stock images though the Web API.
 */
class GetListTest extends WebapiAbstract
{
    private const RESOURCE_PATH = '/V1/adobestock/asset/search';
    private const SERVICE_NAME = 'adobeStockAssetApiAssetRepositoryV1';

    /**
     * Test getting the Adobe Stock image by search criteria.
     *
     * @magentoApiDataFixture ../../../../app/code/Magento/AdobeStockAsset/Test/_files/asset.php
     *
     * @return void
     */
    public function testGetList(): void
    {
        /** @var SearchCriteriaBuilder $searchCriteriaBuilder */
        $searchCriteriaBuilder = Bootstrap::getObjectManager()->create(SearchCriteriaBuilder::class);

        $searchCriteriaBuilder->setPageSize(2);
        $searchCriteriaBuilder->setCurrentPage(1);

        /** @var FilterBuilder $filterBuilder */
        $filterBuilder = Bootstrap::getObjectManager()->create(FilterBuilder::class);
        $idFilter = $filterBuilder->setField('id')
            ->setValue('1')
            ->setConditionType('eq')
            ->create();

        /** @var FilterGroupBuilder $filterGroupBuilder */
        $filterGroupBuilder = Bootstrap::getObjectManager()->create(FilterGroupBuilder::class);
        $filterGroup = $filterGroupBuilder->addFilter($idFilter)->create();

        /** @var SortOrderBuilder $sortOrderBuilder */
        $sortOrderBuilder = Bootstrap::getObjectManager()->create(SortOrderBuilder::class);

        $sortOrder = $sortOrderBuilder->setField('id')
            ->setDirection(SortOrder::SORT_DESC)
            ->create();

        $searchCriteria = $searchCriteriaBuilder->create();
        $searchCriteria->setSortOrders([$sortOrder]);
        $searchCriteria->setFilterGroups([$filterGroup]);

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

        $response = $this->_webApiCall($serviceInfo, $requestData);

        $this->assertArrayHasKey('search_criteria', $response);
        $this->assertArrayHasKey('total_count', $response);
        $this->assertArrayHasKey('items', $response);

        $this->assertEquals($requestData['search_criteria'], $response['search_criteria']);
        $this->assertTrue($response['total_count'] > 0);
        $this->assertTrue(count($response['items']) > 0);

        $this->assertNotNull($response['items'][0]['id']);
        $this->assertEquals('1', $response['items'][0]['id']);
    }
}
