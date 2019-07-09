<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace AdobeStockClient\Test\Unit\Model\SearchParametersProvider;

use AdobeStock\Api\Models\SearchParameters;
use Magento\AdobeStockClient\Model\SearchParametersProvider\Sorting;
use Magento\Framework\Api\SearchCriteriaInterface;
use Magento\Framework\Api\SortOrder;
use Magento\Framework\TestFramework\Unit\Helper\ObjectManager;
use PHPUnit\Framework\TestCase;

/**
 * Test apply selected sorting.
 */
class SortingTest extends TestCase
{
    /**
     * @var ObjectManager
     */
    private $objectManager;

    /**
     * @var Sorting
     */
    private $sorting;

    /**
     * Prepare test objects.
     */
    public function setUp(): void
    {
        $this->objectManager = new ObjectManager($this);
        $this->sorting = $this->objectManager->getObject(Sorting::class);
    }

    /**
     * Test apply sorting with founded sort field.
     */
    public function testApplyWithFoundedField(): void
    {
        /** @var SearchCriteriaInterface|\PHPUnit_Framework_MockObject_MockObject $searchCriteriaMock */
        $searchCriteriaMock = $this->getMockBuilder(SearchCriteriaInterface::class)
            ->setMethods(['getSortOrders'])
            ->disableOriginalConstructor()
            ->getMockForAbstractClass();
        /** @var SearchParameters|\PHPUnit_Framework_MockObject_MockObject $searchParameters */
        $searchParameters = $this->getMockBuilder(SearchParameters::class)
            ->setMethods(['setOrder'])
            ->disableOriginalConstructor()
            ->getMock();
        $sortOrderMock = $this->getMockBuilder(SortOrder::class)
            ->setMethods(['getField'])
            ->disableOriginalConstructor()
            ->getMock();
        $searchCriteriaMock->expects($this->once())
            ->method('getSortOrders')
            ->willReturn([$sortOrderMock]);
        $sortOrderMock->expects($this->once())
            ->method('getField')
            ->willReturn('relevance');
        $searchParameters->expects($this->once())
            ->method('setOrder')
            ->with('RELEVANCE');
        $methodResult = $this->sorting->apply($searchCriteriaMock, $searchParameters);
        $this->assertInstanceOf(SearchParameters::class, $methodResult);
    }

    /**
     * Test apply sorting without founded sort field.
     */
    public function testApplyWithoutFoundedField(): void
    {
        /** @var SearchCriteriaInterface|\PHPUnit_Framework_MockObject_MockObject $searchCriteriaMock */
        $searchCriteriaMock = $this->getMockBuilder(SearchCriteriaInterface::class)
            ->setMethods(['getSortOrders'])
            ->disableOriginalConstructor()
            ->getMockForAbstractClass();
        /** @var SearchParameters|\PHPUnit_Framework_MockObject_MockObject $searchParameters */
        $searchParameters = $this->getMockBuilder(SearchParameters::class)
            ->setMethods(['setOrder'])
            ->disableOriginalConstructor()
            ->getMock();
        $sortOrderMock = $this->getMockBuilder(SortOrder::class)
            ->setMethods(['getField'])
            ->disableOriginalConstructor()
            ->getMock();
        $searchCriteriaMock->expects($this->once())
            ->method('getSortOrders')
            ->willReturn([$sortOrderMock]);
        $sortOrderMock->expects($this->once())
            ->method('getField')
            ->willReturn('test_field_name');
        $searchParameters->expects($this->never())
            ->method('setOrder');
        $methodResult = $this->sorting->apply($searchCriteriaMock, $searchParameters);
        $this->assertInstanceOf(SearchParameters::class, $methodResult);
    }
}
