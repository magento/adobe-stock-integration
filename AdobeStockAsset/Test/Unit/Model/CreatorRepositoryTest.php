<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Magento\AdobeStockAsset\Test\Unit\Model;

use Magento\AdobeStockAsset\Model\CreatorRepository;
use PHPUnit\Framework\TestCase;
use PHPUnit_Framework_MockObject_MockObject as MockObject;
use Magento\AdobeStockAsset\Model\ResourceModel\Creator as ResourceModel;
use Magento\AdobeStockAsset\Model\ResourceModel\Creator\CollectionFactory as CreatorCollectionFactory;
use Magento\AdobeStockAsset\Model\CreatorFactory;
use Magento\Framework\Api\ExtensionAttribute\JoinProcessorInterface;
use Magento\Framework\Api\SearchCriteria\CollectionProcessorInterface;
use Magento\AdobeStockAssetApi\Api\Data\CreatorSearchResultsInterfaceFactory;
use Magento\Framework\Api\SearchCriteriaInterface;
use Magento\AdobeStockAssetApi\Api\Data\CreatorInterface;

/**
 * Category repository test.
 */
class CreatorRepositoryTest extends TestCase
{

    /**
     * @var MockObject|ResourceModel $resourceModel
     */
    private $resourceModel;

    /**
     * @var MockObject|CreatorCollectionFactory
     */
    private $creatorCollectionFactory;

    /**
     * @var MockObject|CreatorFactory $creatorFactory
     */
    private $creatorFactory;

    /**
     * @var MockObject|JoinProcessorInterface $joinProcessorInterface
     */
    private $joinProcessorInterface;

    /**
     * @var MockObject|CollectionProcessorInterface $collectionProcessorInterface
     */
    private $collectionProcessorInterface;

    /**
     * @var MockObject|CreatorSearchResultsInterfaceFactory $creatorSearchResultInterfaceFactory
     */
    private $creatorSearchResultInterfaceFactory;

    /**
     * @var CreatorRepository
     */
    private $creatorRepository;

    /**
     * @inheritDoc
     */
    public function setUp(): void
    {
        $this->resourceModel = $this->createMock(ResourceModel::class);
        $this->creatorCollectionFactory = $this->createMock(CreatorCollectionFactory::class);
        $this->creatorFactory = $this->createMock(CreatorFactory::class);
        $this->joinProcessorInterface = $this->createMock(JoinProcessorInterface::class);
        $this->collectionProcessorInterface = $this->createMock(CollectionProcessorInterface::class);
        $this->creatorSearchResultInterfaceFactory = $this->createMock(CreatorSearchResultsInterfaceFactory::class);

        $this->creatorRepository = new CreatorRepository(
            $this->resourceModel,
            $this->creatorCollectionFactory,
            $this->creatorFactory,
            $this->joinProcessorInterface,
            $this->collectionProcessorInterface,
            $this->creatorSearchResultInterfaceFactory
        );
    }

    /**
     * Test get list
     */
    public function testGetList(): void
    {
        /** @var MockObject|SearchCriteriaInterface $searchCriteria */
        $searchCriteria = $this->createMock(
            \Magento\Framework\Api\SearchCriteriaInterface::class
        );
        $collection = $this->createMock(
            \Magento\AdobeStockAsset\Model\ResourceModel\Category\Collection::class
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
            \Magento\AdobeStockAssetApi\Api\Data\CreatorSearchResultsInterface::class
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
     * Test get By id.
     */
    public function testGetById(): void
    {
        $creatorMock = $this->createMock(\Magento\AdobeStockAsset\Model\Creator::class);
        $this->creatorFactory->expects($this->once())
            ->method('create')
            ->willReturn($creatorMock);
        $this->resourceModel->expects($this->once())
            ->method('load')
            ->willReturnSelf();
        $creatorMock->expects($this->once())
            ->method('getId')
            ->willReturn(2);
        $this->assertInstanceOf(CreatorInterface::class, $this->creatorRepository->getById(2));
    }

    /**
     * Test get By id with exception.
     *
     * @expectedException Magento\Framework\Exception\NoSuchEntityException
     * @exceptedExceptionMessage Object with id 2 does not exist
     */
    public function testGetByIdWithException(): void
    {
        $creatorMock = $this->createMock(\Magento\AdobeStockAsset\Model\Creator::class);
        $this->creatorFactory->expects($this->once())
            ->method('create')
            ->willReturn($creatorMock);
        $this->resourceModel->expects($this->once())
            ->method('load')
            ->willReturnSelf();
        $creatorMock->expects($this->once())
            ->method('getId')
            ->willReturn(null);
        $this->creatorRepository->getById(2);
    }
}
