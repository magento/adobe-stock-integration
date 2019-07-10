<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

declare(strict_types=1);

namespace Magento\AdobeStockClient\Test\Integration\Model;

use AdobeStock\Api\Client\AdobeStock;
use AdobeStock\Api\Models\StockFile;
use AdobeStock\Api\Response\SearchFiles as SearchFilesResponse;
use Magento\AdobeStockClient\Model\Client;
use Magento\AdobeStockClient\Model\ConnectionFactory;
use Magento\Framework\Api\FilterBuilder;
use Magento\Framework\Api\Search\SearchCriteriaBuilder;
use Magento\Framework\Api\Search\SearchResultInterface;
use Magento\Framework\Exception\IntegrationException;
use Magento\TestFramework\Helper\Bootstrap;
use PHPUnit\Framework\TestCase;

/**
 * Test client for communication to Adobe Stock API.
 */
class ClientTest extends TestCase
{
    /**
     * @var Bootstrap
     */
    private $objectManager;

    /**
     * @var SearchCriteriaBuilder
     */
    private $searchCriteriaBuilder;

    /**
     * @var FilterBuilder
     */
    private $filterBuilder;

    /**
     * @var Client
     */
    private $client;

    /**
     * @var ConnectionFactory|\PHPUnit_Framework_MockObject_MockObject
     */
    private $connectionFactoryMock;

    /**
     * Prepare objects.
     */
    protected function setUp(): void
    {
        $this->objectManager = Bootstrap::getObjectManager();
        $this->searchCriteriaBuilder = $this->objectManager->get(SearchCriteriaBuilder::class);
        $this->filterBuilder = $this->objectManager->get(FilterBuilder::class);
        $this->connectionFactoryMock = $this->getMockBuilder(ConnectionFactory::class)
            ->setMethods(['create'])
            ->disableOriginalConstructor()
            ->getMock();
        $this->client = $this->objectManager->create(
            Client::class,
            [
                'connectionFactory' => $this->connectionFactoryMock
            ]
        );
    }

    /**
     * Test search images.
     *
     * @throws IntegrationException
     * @magentoConfigFixture default_store adobe_stock/integration/api_key test_api_key
     * @magentoConfigFixture default_store adobe_stock/integration/environment test_environment
     * @magentoConfigFixture default_store adobe_stock/integration/product_name test_product_name
     */
    public function testSearch(): void
    {
        $filter = $this->filterBuilder->setConditionType('fulltext')
            ->setField('keyword_search')
            ->setValue('test_value')
            ->create();
        $this->searchCriteriaBuilder->addFilter($filter);
        $this->setupConnectionMock();
        $searchResult = $this->client->search($this->searchCriteriaBuilder->create());
        $this->assertInstanceOf(SearchResultInterface::class, $searchResult);
    }

    /**
     * Setup connection mock.
     */
    private function setupConnectionMock(): void
    {
        $adobeStockMock = $this->getMockBuilder(AdobeStock::class)
            ->setMethods(['searchFilesInitialize'])
            ->disableOriginalConstructor()
            ->getMock();
        $adobeStockSearchResultMock = $this->getMockBuilder(AdobeStock::class)
            ->setMethods(['getNextResponse'])
            ->disableOriginalConstructor()
            ->getMock();
        $searchFilesResponseMock = $this->getMockBuilder(SearchFilesResponse::class)
            ->setMethods(['getFiles'])
            ->disableOriginalConstructor()
            ->getMock();
        $foundFiles = $this->getFoundFiles();
        $searchFilesResponseMock->expects($this->once())
            ->method('getFiles')
            ->willReturn($foundFiles);
        $adobeStockSearchResultMock->expects($this->once())
            ->method('getNextResponse')
            ->willReturn($searchFilesResponseMock);
        $adobeStockMock->expects($this->once())
            ->method('searchFilesInitialize')
            ->willReturn($adobeStockSearchResultMock);
        $this->connectionFactoryMock->expects($this->once())
            ->method('create')
            ->with('', 'magento-adobe-stock-integration', 'PROD')
            ->willReturn($adobeStockMock);
    }

    /**
     * Result files.
     *
     * @return StockFile[]
     */
    private function getFoundFiles(): array
    {
        $result = [];
        $resultFiles = [
            [
                'id' => 1,
                'comp_url' => 'https://test.url/1',
                'thumbnail_240_url' => 'https://test.url/1',
                'width' => 110,
                'height' => 210,
            ],
            [
                'id' => 2,
                'comp_url' => 'https://test.url/2',
                'thumbnail_240_url' => 'https://test.url/2',
                'width' => 120,
                'height' => 220,
            ],
            [
                'id' => 3,
                'comp_url' => 'https://test.url/3',
                'thumbnail_240_url' => 'https://test.url/3',
                'width' => 130,
                'height' => 230,
            ],
        ];

        foreach ($resultFiles as $fileData) {
            $result[] = $this->objectManager->create(
                StockFile::class,
                [
                    'raw_response' => $fileData
                ]
            );
        }

        return $result;
    }
}
