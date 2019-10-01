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
use AdobeStock\Api\Request\SearchFiles as SearchFilesRequest;
use Magento\AdobeImsApi\Api\Data\UserProfileInterface;
use Magento\AdobeStockClient\Model\Client;
use Magento\AdobeStockClient\Model\ConnectionFactory;
use Magento\Authorization\Model\UserContextInterface;
use Magento\Framework\Api\FilterBuilder;
use Magento\Framework\Api\Search\SearchCriteriaBuilder;
use Magento\Framework\Api\Search\SearchResultInterface;
use Magento\Framework\Exception\IntegrationException;
use Magento\TestFramework\Helper\Bootstrap;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Magento\Framework\Api\Search\SearchCriteriaInterface;
use Magento\AdobeImsApi\Api\UserProfileRepositoryInterface;

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
     * @var AdobeStock|MockObject
     */
    private $connection;

    /**
     * @var MockObject $userContextMock
     */
    private $userContextMock;

    /**
     * @var MockObject $userProfile
     */
    private $userProfile;

    /**
     * Prepare objects.
     */
    protected function setUp(): void
    {
        $this->connection = $this->getMockBuilder(AdobeStock::class)
            ->setMethods(['searchFilesInitialize', 'getNextResponse'])
            ->disableOriginalConstructor()
            ->getMock();
        $connectionFactory = $this->getMockBuilder(ConnectionFactory::class)
            ->setMethods(['create'])
            ->disableOriginalConstructor()
            ->getMock();
        $connectionFactory->expects($this->once())
            ->method('create')
            ->willReturn($this->connection);
        $this->userContextMock = $this->createMock(\Magento\Authorization\Model\UserContextInterface::class);
        $this->userProfile = $this->createMock(UserProfileRepositoryInterface::class);
        $this->client = Bootstrap::getObjectManager()->create(
            Client::class,
            [
                'connectionFactory' => $connectionFactory,
                'userProfileRepository' => $this->userProfile,
                'userContext' => $this->userContextMock,
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

        $response = $this->getMockBuilder(SearchFilesResponse::class)
            ->setMethods(['getFiles', 'getNbResults'])
            ->disableOriginalConstructor()
            ->getMock();
        $response->expects($this->once())
            ->method('getFiles')
            ->willReturn($this->getStockFiles());
        $response->expects($this->once())
            ->method('getNbResults')
            ->willReturn(3);
        $userProfileInterface = $this->createMock(UserProfileInterface::class);
        $this->userProfile->expects($this->once())->method('getByUserId')->willReturn($userProfileInterface);
        $this->userContextMock->expects($this->once())->method('getUserId')->willReturn(1);
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
                ),
                null
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
