<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Magento\AdobeStockImage\Test\Api;

use Magento\Framework\Api\FilterBuilder;
use Magento\Framework\Api\Search\FilterGroupBuilder;
use Magento\Framework\Api\Search\SearchCriteriaBuilder;
use Magento\Framework\Api\SortOrder;
use Magento\Framework\Api\SortOrderBuilder;
use Magento\Framework\Webapi\Rest\Request;
use Magento\TestFramework\Helper\Bootstrap;
use Magento\TestFramework\TestCase\WebapiAbstract;

/**
 * Class SearchExecuteTest
 * @package Magento\AdobeStockImage\Test\Api
 */
class SearchExecuteTest extends WebapiAbstract
{
    private const RESOURCE_PATH = '/V1/adobestock/search';
    private const SERVICE_NAME = 'adobeStockImageApiGetImageListV1';

    public function testSearchExecute(): void
    {
        /** @var SearchCriteriaBuilder $searchCriteriaBuilder */
        $searchCriteriaBuilder = Bootstrap::getObjectManager()->create(SearchCriteriaBuilder::class);

        $searchCriteriaBuilder->setPageSize(2);
        $searchCriteriaBuilder->setCurrentPage(1);

        /** @var FilterBuilder $filterBuilder */
        $filterBuilder = Bootstrap::getObjectManager()->create(FilterBuilder::class);
        /** @var FilterGroupBuilder $filterGroupBuilder */
        $filterGroupBuilder = Bootstrap::getObjectManager()->create(FilterGroupBuilder::class);
        $filterGroup = $filterGroupBuilder->addFilter(
            $filterBuilder->setField('orientation_filter')
                ->setValue('PANORAMIC')
                ->create()
        )->create();

        /** @var SortOrderBuilder $sortOrderBuilder */
        $sortOrderBuilder = Bootstrap::getObjectManager()->create(SortOrderBuilder::class);

        /** @var SortOrder $sortOrder */
        $sortOrder = $sortOrderBuilder->setField('id')
            ->setDirection(SortOrder::SORT_DESC)
            ->create();

        $searchCriteria = $searchCriteriaBuilder->create();

        $searchCriteria->setSortOrders([$sortOrder]);
        $searchCriteria->setRequestName('adobe_stock_images_listing_data_source');
        $searchCriteria->setFilterGroups([$filterGroup]);

        $requestData = ['searchCriteria' => $searchCriteria->__toArray()];

        $serviceInfo = [
            'rest' => [
                'resourcePath' => self::RESOURCE_PATH . '?' . http_build_query($requestData),
                'httpMethod' => Request::HTTP_METHOD_GET,
            ],
            'soap' => [
                'service' => self::SERVICE_NAME,
                'operation' => self::SERVICE_NAME . 'Execute',
            ],
        ];

        $response = $this->_webApiCall($serviceInfo, $requestData);

        $this->assertArrayHasKey('search_criteria', $response);
        $this->assertArrayHasKey('total_count', $response);
        $this->assertArrayHasKey('items', $response);

        $this->assertGreaterThan(0, $response['total_count']);
        $this->assertGreaterThan(0, count($response['items']));

        $this->assertNotNull($response['items'][0]['id']);
    }
}
