<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Magento\AdobeStockAsset\Test\Api\AssetRepository;

use Magento\Framework\Webapi\Rest\Request;
use Magento\TestFramework\TestCase\WebapiAbstract;

/**
 * Testing search asset web api
 */
class AssetListTest extends WebapiAbstract
{
    private const RESOURCE_PATH = '/V1/adobestock/asset/search';
    private const SERVICE_NAME = 'adobeStockAssetApiAssetRepositoryV1';

    /**
     * Test List
     *
     * @magentoApiDataFixture ../../../../app/code/Magento/AdobeStockAsset/Test/_files/asset.php
     *
     * @return void
     */
    public function testGetList(): void
    {
        $searchCriteria = [
            'searchCriteria' => [
                'filter_groups' => [
                    [
                        'filters' => [
                            [
                                'field' => 'id',
                                'value' => '1',
                                'condition_type' => 'eq',
                            ]
                        ]
                    ]
                ],
                'current_page' => 1,
                'page_size' => 2,
            ],
        ];

        $serviceInfo = [
            'rest' => [
                'resourcePath' => self::RESOURCE_PATH . '?' . http_build_query($searchCriteria),
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

        $this->assertEquals($searchCriteria['searchCriteria'], $response['search_criteria']);
        $this->assertTrue($response['total_count'] > 0);
        $this->assertTrue(count($response['items']) > 0);

        $this->assertNotNull($response['items'][0]['id']);
        $this->assertEquals('1', $response['items'][0]['id']);
    }
}
