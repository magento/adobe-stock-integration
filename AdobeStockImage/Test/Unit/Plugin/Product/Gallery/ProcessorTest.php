<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Magento\AdobeStockImage\Test\Unit\Plugin\Product\Gallery;

use Magento\AdobeStockAssetApi\Api\AssetRepositoryInterface;
use Magento\AdobeStockAssetApi\Api\Data\AssetInterface;
use Magento\AdobeStockAssetApi\Api\Data\AssetSearchResultsInterface;
use Magento\AdobeStockImage\Plugin\Product\Gallery\Processor;
use Magento\Catalog\Model\Product;
use Magento\Catalog\Model\Product\Gallery\Processor as ProcessorSubject;
use Magento\Framework\Api\Filter;
use Magento\Framework\Api\FilterBuilder;
use Magento\Framework\Api\SearchCriteria;
use Magento\Framework\Api\SearchCriteriaBuilder;
use Magento\Framework\Api\SearchCriteriaBuilderFactory;
use Magento\Framework\TestFramework\Unit\Helper\ObjectManager;
use PHPUnit\Framework\TestCase;

/**
 * Test process after remove image.
 */
class ProcessorTest extends TestCase
{
    /**
     * @var ObjectManager
     */
    private $objectManager;

    /**
     * @var SearchCriteriaBuilderFactory|\PHPUnit_Framework_MockObject_MockObject
     */
    private $searchCriteriaBuilderFactoryMock;

    /**
     * @var FilterBuilder|\PHPUnit_Framework_MockObject_MockObject
     */
    private $filterBuilderMock;

    /**
     * @var AssetRepositoryInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    private $assetRepositoryInterfaceMock;

    /**
     * @var Processor
     */
    private $processor;

    /**
     * Prepare test objects.
     */
    public function setUp()
    {
        $this->objectManager = new ObjectManager($this);
        $this->searchCriteriaBuilderFactoryMock = $this->getMockBuilder(SearchCriteriaBuilderFactory::class)
            ->setMethods(['create'])
            ->disableOriginalConstructor()
            ->getMock();
        $this->filterBuilderMock = $this->getMockBuilder(FilterBuilder::class)
            ->setMethods(['setValue', 'setConditionType', 'setField', 'create'])
            ->disableOriginalConstructor()
            ->getMock();
        $this->assetRepositoryInterfaceMock = $this->getMockBuilder(AssetRepositoryInterface::class)
            ->setMethods(['getList', 'delete'])
            ->disableOriginalConstructor()
            ->getMockForAbstractClass();
        $this->processor = $this->objectManager->getObject(
            Processor::class,
            [
                'assetRepository' => $this->assetRepositoryInterfaceMock,
                'filterBuilder' => $this->filterBuilderMock,
                'searchCriteriaBuilderFactory' => $this->searchCriteriaBuilderFactoryMock
            ]
        );
    }

    /**
     * Test delete Adobe's stock asset after image was deleted
     * with image file path.
     */
    public function testAfterRemoveImageWithFile()
    {
        /** @var ProcessorSubject $subject */
        $subject = $this->objectManager->getObject(ProcessorSubject::class);
        /** @var ProcessorSubject $result */
        $result = $this->objectManager->getObject(ProcessorSubject::class);
        /** @var Product $product */
        $product = $this->objectManager->getObject(Product::class);
        $file = 'some_file_path';
        $this->filterBuilderMock->expects($this->once())
            ->method('setField')
            ->with('path')
            ->willReturn($this->filterBuilderMock);
        $this->filterBuilderMock->expects($this->once())
            ->method('setConditionType')
            ->with('eq')
            ->willReturn($this->filterBuilderMock);
        $this->filterBuilderMock->expects($this->once())
            ->method('setValue')
            ->with($file)
            ->willReturn($this->filterBuilderMock);
        $filterMock = $this->getMockBuilder(Filter::class)
            ->disableOriginalConstructor()
            ->getMockForAbstractClass();
        $this->filterBuilderMock->expects($this->once())
            ->method('create')
            ->willReturn($filterMock);
        $searchCriteriaBuilderMock = $this->getMockBuilder(SearchCriteriaBuilder::class)
            ->setMethods(['addFilters', 'create'])
            ->disableOriginalConstructor()
            ->getMock();
        $this->searchCriteriaBuilderFactoryMock->expects($this->once())
            ->method('create')
            ->willReturn($searchCriteriaBuilderMock);
        $searchCriteriaBuilderMock->expects($this->once())
            ->method('addFilters')
            ->with([$filterMock]);
        $searchCriteriaMock = $this->getMockBuilder(SearchCriteria::class)
            ->disableOriginalConstructor()
            ->getMockForAbstractClass();
        $searchCriteriaBuilderMock->expects($this->once())
            ->method('create')
            ->willReturn($searchCriteriaMock);
        $assertSearchResultMock = $this->getMockBuilder(AssetSearchResultsInterface::class)
            ->disableOriginalConstructor()
            ->setMethods(['getItems'])
            ->getMockForAbstractClass();
        $this->assetRepositoryInterfaceMock->expects($this->once())
            ->method('getList')
            ->with($searchCriteriaMock)
            ->willReturn($assertSearchResultMock);
        $assetMock = $this->getMockBuilder(AssetInterface::class)
            ->disableOriginalConstructor()
            ->getMockForAbstractClass();
        $assertSearchResultMock->expects($this->once())
            ->method('getItems')
            ->willReturn([$assetMock]);
        $this->assetRepositoryInterfaceMock->expects($this->once())
            ->method('delete')
            ->with($assetMock);
        $methodResult = $this->processor->afterRemoveImage($subject, $result, $product, $file);
        $this->assertInstanceOf(ProcessorSubject::class, $methodResult);
    }

    /**
     * Test delete Adobe's stock asset after image was deleted
     * without image file path.
     */
    public function testAfterRemoveImageWithoutFile()
    {
        /** @var ProcessorSubject $subject */
        $subject = $this->objectManager->getObject(ProcessorSubject::class);
        /** @var ProcessorSubject $result */
        $result = $this->objectManager->getObject(ProcessorSubject::class);
        /** @var Product $product */
        $product = $this->objectManager->getObject(Product::class);
        $file = null;
        $this->filterBuilderMock->expects($this->never())
            ->method('setField')
            ->with('path')
            ->willReturn($this->filterBuilderMock);
        $this->filterBuilderMock->expects($this->never())
            ->method('setConditionType')
            ->with('eq')
            ->willReturn($this->filterBuilderMock);
        $this->filterBuilderMock->expects($this->never())
            ->method('setValue')
            ->with($file)
            ->willReturn($this->filterBuilderMock);
        $filterMock = $this->getMockBuilder(Filter::class)
            ->disableOriginalConstructor()
            ->getMockForAbstractClass();
        $this->filterBuilderMock->expects($this->never())
            ->method('create')
            ->willReturn($filterMock);
        $searchCriteriaBuilderMock = $this->getMockBuilder(SearchCriteriaBuilder::class)
            ->setMethods(['addFilters', 'create'])
            ->disableOriginalConstructor()
            ->getMock();
        $this->searchCriteriaBuilderFactoryMock->expects($this->never())
            ->method('create')
            ->willReturn($searchCriteriaBuilderMock);
        $searchCriteriaBuilderMock->expects($this->never())
            ->method('addFilters')
            ->with([$filterMock]);
        $searchCriteriaMock = $this->getMockBuilder(SearchCriteria::class)
            ->disableOriginalConstructor()
            ->getMockForAbstractClass();
        $searchCriteriaBuilderMock->expects($this->never())
            ->method('create')
            ->willReturn($searchCriteriaMock);
        $assertSearchResultMock = $this->getMockBuilder(AssetSearchResultsInterface::class)
            ->disableOriginalConstructor()
            ->setMethods(['getItems'])
            ->getMockForAbstractClass();
        $this->assetRepositoryInterfaceMock->expects($this->never())
            ->method('getList')
            ->with($searchCriteriaMock)
            ->willReturn($assertSearchResultMock);
        $assetMock = $this->getMockBuilder(AssetInterface::class)
            ->disableOriginalConstructor()
            ->getMockForAbstractClass();
        $assertSearchResultMock->expects($this->never())
            ->method('getItems')
            ->willReturn([$assetMock]);
        $this->assetRepositoryInterfaceMock->expects($this->never())
            ->method('delete')
            ->with($assetMock);
        $methodResult = $this->processor->afterRemoveImage($subject, $result, $product, $file);
        $this->assertInstanceOf(ProcessorSubject::class, $methodResult);
    }
}
