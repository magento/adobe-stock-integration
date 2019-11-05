<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

declare(strict_types=1);

namespace Magento\AdobeStockClient\Test\Integration\Model;

use AdobeStock\Api\Models\StockFile;
use AdobeStock\Api\Request\SearchFiles as SearchFilesRequest;
use AdobeStock\Api\Response\SearchFiles as SearchFilesResponse;
use Magento\AdobeStockClient\Model\Client;
use Magento\AdobeStockClient\Model\ConnectionWrapper;
use Magento\AdobeStockClient\Model\ConnectionWrapperFactory;
use Magento\Framework\Api\FilterBuilder;
use Magento\Framework\Api\Search\SearchCriteriaBuilder;
use Magento\Framework\Api\Search\SearchCriteriaInterface;
use Magento\Framework\Api\Search\SearchResultInterface;
use Magento\Framework\Exception\IntegrationException;
use Magento\TestFramework\Helper\Bootstrap;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

/**
 * Test client for communication to Adobe Stock API.
 */
class ClientTest extends TestCase
{
    /**
     * @var Client
     */
    private $client;

    /**
     * @var ConnectionWrapper|MockObject
     */
    private $connection;

    /**
     * Prepare objects.
     */
    protected function setUp(): void
    {
        $this->connection = $this->createMock(ConnectionWrapper::class);

        /** @var ConnectionWrapperFactory|MockObject $connectionFactory */
        $connectionFactory = $this->createMock(ConnectionWrapperFactory::class);
        $connectionFactory->expects($this->once())
            ->method('create')
            ->willReturn($this->connection);

        $this->client = Bootstrap::getObjectManager()->create(
            Client::class,
            [
                'connectionFactory' => $connectionFactory
            ]
        );
    }

    /**
     * Test with found images.
     *
     * @throws IntegrationException
     */
    public function testSearch(): void
    {
        $words = 'pear';

        $response = $this->createMock(SearchFilesResponse::class);
        $response->expects($this->once())
            ->method('getFiles')
            ->willReturn($this->getStockFiles());
        $response->expects($this->once())
            ->method('getNbResults')
            ->willReturn(3);
        $this->connection->expects($this->once())
            ->method('searchFilesInitialize')
            ->with(
                $this->callback(
                    function (SearchFilesRequest $searchFiles) use ($words) {
                        return $searchFiles->getLocale() == 'en_US'
                            && in_array('id', $searchFiles->getResultColumns())
                            && in_array('nb_results', $searchFiles->getResultColumns())
                            && $searchFiles->getSearchParams()->getWords() == $words;
                    }
                )
            );
        $this->connection->expects($this->once())
            ->method('getNextResponse')
            ->willReturn($response);
        $searchResult = $this->client->search($this->getSearchCriteria($words));

        $this->assertInstanceOf(SearchResultInterface::class, $searchResult);
        $this->assertEquals(3, $searchResult->getTotalCount());
        $this->assertEquals(
            'https://test.url/2',
            $searchResult->getItems()[1]->getCustomAttributes()['comp_url']->getValue()
        );
    }

    /**
     * @param string $words
     * @return SearchCriteriaInterface
     */
    private function getSearchCriteria(string $words): SearchCriteriaInterface
    {
        $filter = Bootstrap::getObjectManager()->get(FilterBuilder::class)
            ->setConditionType('fulltext')
            ->setField('words')
            ->setValue($words)
            ->create();
        return Bootstrap::getObjectManager()->get(SearchCriteriaBuilder::class)
            ->addFilter($filter)
            ->create();
    }

    /**
     * Result files.
     *
     * @return StockFile[]
     */
    private function getStockFiles(): array
    {
        $stockFilesData = [
            [
                'id' => 1,
                'comp_url' => 'https://test.url/1',
                'thumbnail_240_url' => 'https://test.url/1',
                'width' => 110,
                'height' => 210,
                'some_bool_param' => false,
                'some_nullable_param' => null,
                'category' => [
                    'id' => 1,
                    'N
                    name' => 'Test'
                ]
            ],
            [
                'id' => 2,
                'comp_url' => 'https://test.url/2',
                'thumbnail_240_url' => 'https://test.url/2',
                'width' => 120,
                'height' => 220,
                'some_bool_params' => false,
                'some_nullable_param' => 1,
                'category' => [
                    'id' => 1,
                    'N
                    name' => 'Test'
                ]
            ],
            [
                'id' => 3,
                'comp_url' => 'https://test.url/3',
                'thumbnail_240_url' => 'https://test.url/3',
                'width' => 130,
                'height' => 230,
                'some_bool_params' => true,
                'some_nullable_param' => 2,
                'category' => [
                    'id' => 1,
                    'N
                    name' => 'Test'
                ]
            ],
        ];

        $stockFiles = [];
        foreach ($stockFilesData as $stockFileData) {
            $stockFiles[] = new StockFile($stockFileData);
        }

        return $stockFiles;
    }
}
