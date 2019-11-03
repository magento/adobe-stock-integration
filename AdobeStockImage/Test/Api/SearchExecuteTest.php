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

    private const SERVICE_READ_NAME = 'adobeStockSearchV1';

    private const SERVICE_VERSION = 'V1';

    /**
     * @var ObjectManagerInterface
     */
    protected $objectManager;

    public function testSearchExecute(): void
    {
        $searchCriteria = $this->getSearchData();

        $serviceInfo = [
            'rest' => [
                'resourcePath' => self::RESOURCE_PATH . '?' . http_build_query($searchCriteria),
                'httpMethod' => Request::HTTP_METHOD_GET,
            ],
            'soap' => [
                'service' => self::SERVICE_READ_NAME,
                'operation' => self::SERVICE_READ_NAME . 'execute',
            ],
        ];

        $response = $this->_webApiCall($serviceInfo, $searchCriteria);

        $this->assertArrayHasKey('search_criteria', $response);
        $this->assertArrayHasKey('total_count', $response);
        $this->assertArrayHasKey('items', $response);

        $this->assertTrue($response['total_count'] > 0);
        $this->assertTrue(count($response['items']) > 0);

        $this->assertNotNull($response['items'][0]['id']);
    }

    /**
     * @return array
     */
    private function getSearchData(): array
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

        return $searchCriteria;
    }
}
