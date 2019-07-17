<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

declare(strict_types=1);

namespace Magento\AdobeStockClient\Test\Integration\Model;

use AdobeStock\Api\Client\AdobeStock;
use AdobeStock\Api\Exception\StockApi;
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
use Psr\Log\LoggerInterface;

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
     * @var LoggerInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    private $loggerMock;

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
        $this->loggerMock = $this->getMockBuilder(LoggerInterface::class)
            ->setMethods(['critical'])
            ->disableOriginalConstructor()
            ->getMockForAbstractClass();
        $this->client = $this->objectManager->create(
            Client::class,
            [
                'connectionFactory' => $this->connectionFactoryMock,
                'logger' => $this->loggerMock
            ]
        );
    }

    /**
     * Test with found images.
     *
     * @throws IntegrationException
     */
    public function testSearchWithFoundImages(): void
    {
        $filter = $this->filterBuilder->setConditionType('fulltext')
            ->setField('keyword_search')
            ->setValue('test_value')
            ->create();
        $this->searchCriteriaBuilder->addFilter($filter);
        $this->setupConnectionMock();
        $searchCriteria = $this->searchCriteriaBuilder->create();
        $searchResult = $this->client->search($searchCriteria);
        $this->assertInstanceOf(SearchResultInterface::class, $searchResult);
        $this->assertEquals($searchCriteria, $searchResult->getSearchCriteria());
        $this->assertEquals(3, $searchResult->getTotalCount());
    }

    /**
     * Test without found images.
     *
     * @throws IntegrationException
     */
    public function testSearchWithoutFoundImages(): void
    {
        $filter = $this->filterBuilder->setConditionType('fulltext')
            ->setField('keyword_search')
            ->setValue('test_value')
            ->create();
        $this->searchCriteriaBuilder->addFilter($filter);
        $this->setupConnectionMockWithoutFiles();
        $searchCriteria = $this->searchCriteriaBuilder->create();
        $searchResult = $this->client->search($searchCriteria);
        $this->assertInstanceOf(SearchResultInterface::class, $searchResult);
        $this->assertEquals($searchCriteria, $searchResult->getSearchCriteria());
        $this->assertEquals([], $searchResult->getItems());
        $this->assertEquals(0, $searchResult->getTotalCount());
    }

    /**
     * Test get error during get connection.
     */
    public function testSearchWithConnectionError(): void
    {
        $filter = $this->filterBuilder->setConditionType('fulltext')
            ->setField('keyword_search')
            ->setValue('test_value')
            ->create();
        $this->searchCriteriaBuilder->addFilter($filter);
        $this->setupConnectionWithErrorMock();
        try {
            $this->client->search($this->searchCriteriaBuilder->create());
        } catch (IntegrationException $e) {
            $this->assertEquals(
                'An error occurred during Adobe Stock connection initialization: Test error text',
                $e->getMessage()
            );
        }
    }

    /**
     * Test get error during search images.
     *
     * @throws IntegrationException
     */
    public function testSearchWithSearchError(): void
    {
        $filter = $this->filterBuilder->setConditionType('fulltext')
            ->setField('keyword_search')
            ->setValue('test_value')
            ->create();
        $this->searchCriteriaBuilder->addFilter($filter);
        $this->setupSearchWithErrorMock();
        $searchCriteria = $this->searchCriteriaBuilder->create();
        $this->loggerMock->expects($this->once())
            ->method('critical')
            ->with('Text authorization error');
        $searchResult = $this->client->search($searchCriteria);
        $this->assertInstanceOf(SearchResultInterface::class, $searchResult);
        $this->assertEquals($searchCriteria, $searchResult->getSearchCriteria());
        $this->assertEquals([], $searchResult->getItems());
        $this->assertEquals(0, $searchResult->getTotalCount());
    }

    /**
     * Setup connection mock which will throw exception.
     */
    private function setupConnectionWithErrorMock(): void
    {
        $this->connectionFactoryMock->expects($this->once())
            ->method('create')
            ->with('', 'magento-adobe-stock-integration', 'PROD')
            ->willThrowException(new StockApi('Test error text'));
    }

    /**
     * Setup connection mock which will not return files.
     */
    private function setupSearchWithErrorMock(): void
    {
        $connectionMock = $this->getMockBuilder(AdobeStock::class)
            ->setMethods(['searchFilesInitialize', 'getNextResponse'])
            ->disableOriginalConstructor()
            ->getMock();
        $connectionMock->expects($this->once())
            ->method('searchFilesInitialize');
        $connectionMock->expects($this->once())
            ->method('getNextResponse')
            ->willThrowException(new StockApi('Text authorization error', 403));
        $this->connectionFactoryMock->expects($this->once())
            ->method('create')
            ->with('', 'magento-adobe-stock-integration', 'PROD')
            ->willReturn($connectionMock);
    }

    /**
     * Setup connection mock which will not return files.
     */
    private function setupConnectionMockWithoutFiles(): void
    {
        $connectionMock = $this->getMockBuilder(AdobeStock::class)
            ->setMethods(['searchFilesInitialize', 'getNextResponse'])
            ->disableOriginalConstructor()
            ->getMock();
        $connectionMock->expects($this->once())
            ->method('searchFilesInitialize');
        $responseMock = $this->getMockBuilder(SearchFilesResponse::class)
            ->setMethods(['getFiles', 'getNbResults'])
            ->disableOriginalConstructor()
            ->getMock();
        $connectionMock->expects($this->once())
            ->method('getNextResponse')
            ->willReturn($responseMock);
        $responseMock->expects($this->once())
            ->method('getFiles')
            ->willReturn([]);
        $responseMock->expects($this->once())
            ->method('getNbResults')
            ->willReturn(0);
        $this->connectionFactoryMock->expects($this->once())
            ->method('create')
            ->with('', 'magento-adobe-stock-integration', 'PROD')
            ->willReturn($connectionMock);
    }

    /**
     * Setup connection mock.
     */
    private function setupConnectionMock(): void
    {
        $connectionMock = $this->getMockBuilder(AdobeStock::class)
            ->setMethods(['searchFilesInitialize', 'getNextResponse'])
            ->disableOriginalConstructor()
            ->getMock();
        $connectionMock->expects($this->once())
            ->method('searchFilesInitialize');
        $responseMock = $this->getMockBuilder(SearchFilesResponse::class)
            ->setMethods(['getFiles', 'getNbResults'])
            ->disableOriginalConstructor()
            ->getMock();
        $connectionMock->expects($this->once())
            ->method('getNextResponse')
            ->willReturn($responseMock);
        $responseMock->expects($this->once())
            ->method('getFiles')
            ->willReturn($this->getFoundFiles());
        $responseMock->expects($this->once())
            ->method('getNbResults')
            ->willReturn(3);
        $this->connectionFactoryMock->expects($this->once())
            ->method('create')
            ->with('', 'magento-adobe-stock-integration', 'PROD')
            ->willReturn($connectionMock);
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
