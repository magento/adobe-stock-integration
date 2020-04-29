<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Magento\AdobeStockAsset\Test\Unit\Model;

use Magento\AdobeStockAsset\Model\Category;
use Magento\AdobeStockAsset\Model\CategoryRepository;
use Magento\AdobeStockAsset\Model\ResourceModel\Category\Collection;
use Magento\AdobeStockAsset\Model\ResourceModel\Category\CollectionFactory as CategoryCollectionFactory;
use Magento\AdobeStockAssetApi\Api\Data\CategoryInterface;
use Magento\AdobeStockAssetApi\Api\Data\CategorySearchResultsInterface;
use Magento\AdobeStockAssetApi\Api\Data\CategorySearchResultsInterfaceFactory;
use Magento\AdobeStockAssetApi\Model\Category\Command\DeleteByIdInterface;
use Magento\AdobeStockAssetApi\Model\Category\Command\LoadByIdInterface;
use Magento\AdobeStockAssetApi\Model\Category\Command\SaveInterface;
use Magento\Framework\Api\ExtensionAttribute\JoinProcessorInterface;
use Magento\Framework\Api\SearchCriteria\CollectionProcessorInterface;
use Magento\Framework\Api\SearchCriteriaInterface;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

/**
 * Test for the Adobe Stock Asset Category repository
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class CategoryRepositoryTest extends TestCase
{
    /**
     * @var MockObject|CategoryCollectionFactory $categoryCollectionFactory
     */
    private $categoryCollectionFactory;

    /**
     * @var MockObject|JoinProcessorInterface $joinProcessorInterface
     */
    private $joinProcessorInterface;

    /**
     * @var MockObject|CollectionProcessorInterface $collectionProcessorInterface
     */
    private $collectionProcessorInterface;

    /**
     * @var MockObject|CategorySearchResultsInterfaceFactory $categorySearchResultsInterfaceFactory
     */
    private $categorySearchResultsInterfaceFactory;

    /**
     * @var CategoryRepository $categoryRepository
     */
    private $categoryRepository;

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
        $this->categoryCollectionFactory = $this->createMock(CategoryCollectionFactory::class);
        $this->joinProcessorInterface = $this->createMock(JoinProcessorInterface::class);
        $this->collectionProcessorInterface = $this->createMock(CollectionProcessorInterface::class);
        $this->categorySearchResultsInterfaceFactory = $this->createMock(CategorySearchResultsInterfaceFactory::class);
        $this->loadByIdCommandMock = $this->createMock(LoadByIdInterface::class);
        $this->saveCommandMock = $this->createMock(SaveInterface::class);
        $this->deleteByIdCommandMock = $this->createMock(DeleteByIdInterface::class);

        $this->categoryRepository = new CategoryRepository(
            $this->categoryCollectionFactory,
            $this->joinProcessorInterface,
            $this->collectionProcessorInterface,
            $this->categorySearchResultsInterfaceFactory,
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
        $searchCriteria = $this->createMock(SearchCriteriaInterface::class);

        $collection = $this->createMock(Collection::class);
        $this->categoryCollectionFactory->expects($this->once())
            ->method('create')
            ->willReturn($collection);

        $this->joinProcessorInterface->expects($this->once())
            ->method('process')
            ->with($collection, CategoryInterface::class)
            ->willReturn(null);
        $this->collectionProcessorInterface->expects($this->once())
            ->method('process')
            ->with($searchCriteria, $collection)
            ->willReturn(null);
        $searchResults = $this->createMock(CategorySearchResultsInterface::class);
        $this->categorySearchResultsInterfaceFactory->expects($this->once())
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
        $this->assertEquals($searchResults, $this->categoryRepository->getList($searchCriteria));
    }

    /**
     * Test getById scenario with successful result.
     */
    public function testGetById(): void
    {
        $categoryId = 1;
        $categoryMock = $this->createMock(Category::class);
        $this->loadByIdCommandMock->expects($this->once())
            ->method('execute')
            ->with($categoryId)
            ->willReturn($categoryMock);
        $this->assertInstanceOf(
            CategoryInterface::class,
            $this->categoryRepository->getById($categoryId)
        );
    }
}
