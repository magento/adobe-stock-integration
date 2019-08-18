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
    /**
     * Resource path
     */
    const RESOURCE_PATH = '/V1/adobestock/asset/search';

    /**
     * Service version
     */
    const SERVICE_VERSION = 'V1';

    /**
     * Service name
     */
    const SERVICE_NAME = 'adobeStockAssetRepositoryV1';

    /**
     * Test List
     *
     * @magentoApiDataFixture assetFixtureProvider
     *
     * @return void
     */
    public function testGetList()
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

    /**
     * Asset fixture provider
     *
     * @return void
     */
    public static function assetFixtureProvider()
    {
        require __DIR__ . '/../../_files/api_assets.php';
    }
}
