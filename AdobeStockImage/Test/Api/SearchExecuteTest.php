<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magento\AdobeStockImage\Test\Api;

use Magento\Framework\ObjectManagerInterface;
use Magento\Framework\Webapi\Rest\Request;
use Magento\TestFramework\TestCase\WebapiAbstract;

/**
 * Class SearchExecuteTest
 * @package Magento\AdobeStockImage\Test\Api
 */
class SearchExecuteTest extends WebapiAbstract
{
    private const RESOURCE_PATH = '/V1/adobestock/search';
    private const SERVICE_NAME = 'adobeStockImageApiGetImageListV1';

    /**
     * @var ObjectManagerInterface
     */
    protected $objectManager;

    public function testSearchExecute(): void
    {
        $searchCriteria = [
            'searchCriteria' => [
                'sort_orders' => [
                    [
                        'field' => 'id',
                        'direction' => 'DESC'
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
                'operation' => self::SERVICE_NAME . 'Execute',
            ],
        ];

        $response = $this->_webApiCall($serviceInfo, $searchCriteria);

        $this->assertArrayHasKey('search_criteria', $response);
        $this->assertArrayHasKey('total_count', $response);
        $this->assertArrayHasKey('items', $response);

        $this->assertGreaterThan(0, $response['total_count']);
        $this->assertGreaterThan(0, count($response['items']));

        $this->assertNotNull($response['items'][0]['id']);
    }
}
