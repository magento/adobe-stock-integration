<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Magento\AdobeStockAsset\Test\Unit\Model;

use Magento\AdobeStockAsset\Model\Creator;
use Magento\AdobeStockAsset\Model\CreatorRepository;
use Magento\AdobeStockAsset\Model\ResourceModel\Category\Collection;
use Magento\AdobeStockAsset\Model\ResourceModel\Creator\CollectionFactory as CreatorCollectionFactory;
use Magento\AdobeStockAssetApi\Api\Data\CreatorInterface;
use Magento\AdobeStockAssetApi\Api\Data\CreatorSearchResultsInterface;
use Magento\AdobeStockAssetApi\Api\Data\CreatorSearchResultsInterfaceFactory;
use Magento\AdobeStockAssetApi\Model\Creator\Command\DeleteByIdInterface;
use Magento\AdobeStockAssetApi\Model\Creator\Command\LoadByIdInterface;
use Magento\AdobeStockAssetApi\Model\Creator\Command\SaveInterface;
use Magento\Framework\Api\ExtensionAttribute\JoinProcessorInterface;
use Magento\Framework\Api\SearchCriteria\CollectionProcessorInterface;
use Magento\Framework\Api\SearchCriteriaInterface;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

/**
 * Test for the Adobe Stock Asset Creator repository
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class CreatorRepositoryTest extends TestCase
{
    /**
     * @var MockObject|CreatorCollectionFactory
     */
    private $creatorCollectionFactory;

    /**
     * @var MockObject|JoinProcessorInterface
     */
    private $joinProcessorInterface;

    /**
     * @var MockObject|CollectionProcessorInterface
     */
    private $collectionProcessorInterface;

    /**
     * @var MockObject|CreatorSearchResultsInterfaceFactory
     */
    private $creatorSearchResultInterfaceFactory;

    /**
     * @var CreatorRepository
     */
    private $creatorRepository;

    /**
     * @var LoadByIdInterface|MockObject
     */
    private $loadByIdCommandMock;

    /**
     * @var SaveInterface|MockObject
     */
    private $saveCommandMock;

    /**
     * @var DeleteByIdInterface|MockObject
     */
    private $deleteByIdCommandMock;

    /**
     * @inheritdoc
     */
    protected function setUp(): void
    {
        $this->creatorCollectionFactory = $this->createMock(CreatorCollectionFactory::class);
        $this->joinProcessorInterface = $this->createMock(JoinProcessorInterface::class);
        $this->collectionProcessorInterface = $this->createMock(CollectionProcessorInterface::class);
        $this->creatorSearchResultInterfaceFactory = $this->createMock(CreatorSearchResultsInterfaceFactory::class);
        $this->loadByIdCommandMock = $this->createMock(LoadByIdInterface::class);
        $this->saveCommandMock = $this->createMock(SaveInterface::class);
        $this->deleteByIdCommandMock = $this->createMock(DeleteByIdInterface::class);

        $this->creatorRepository = new CreatorRepository(
            $this->creatorCollectionFactory,
            $this->joinProcessorInterface,
            $this->collectionProcessorInterface,
            $this->creatorSearchResultInterfaceFactory,
            $this->loadByIdCommandMock,
            $this->saveCommandMock,
            $this->deleteByIdCommandMock
        );
    }

    /**
     * Test get list
     */
    public function testGetList(): void
    {
        /** @var MockObject|SearchCriteriaInterface $searchCriteria */
        $searchCriteria = $this->createMock(
            SearchCriteriaInterface::class
        );
        $collection = $this->createMock(
            Collection::class
        );
        $this->creatorCollectionFactory->expects($this->once())
            ->method('create')
            ->willReturn($collection);

        $this->joinProcessorInterface->expects($this->once())
            ->method('process')
            ->with($collection, CreatorInterface::class)
            ->willReturn(null);
        $this->collectionProcessorInterface->expects($this->once())
            ->method('process')
            ->with($searchCriteria, $collection)
            ->willReturn(null);
        $searchResults = $this->createMock(
            CreatorSearchResultsInterface::class
        );
        $this->creatorSearchResultInterfaceFactory->expects($this->once())
            ->method('create')
            ->willReturn($searchResults);
        $collection->expects($this->once())
            ->method('getItems')
            ->willReturn([]);
        $searchResults->expects($this->once())
            ->method('setItems')
            ->willReturnSelf();
        $searchResults->expects($this->once())
            ->method('setSearchCriteria')
            ->willReturnSelf();
        $searchResults->expects($this->once())
            ->method('setTotalCount')
            ->willReturnSelf();
        $this->assertEquals($searchResults, $this->creatorRepository->getList($searchCriteria));
    }

    /**
     * Test getById scenario with successful result.
     */
    public function testGetById(): void
    {
        $creatorId = 1;
        $creatorMock = $this->createMock(Creator::class);
        $this->loadByIdCommandMock->expects($this->once())
            ->method('execute')
            ->with($creatorId)
            ->willReturn($creatorMock);
        $this->assertInstanceOf(
            CreatorInterface::class,
            $this->creatorRepository->getById($creatorId)
        );
    }
}
