<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Magento\AdobeStockClient\Test\Unit\Model\SearchParametersProvider;

use AdobeStock\Api\Models\SearchParameters;
use Magento\AdobeStockClient\Model\SearchParametersProvider\Sorting;
use Magento\Framework\Api\SearchCriteriaInterface;
use Magento\Framework\Api\SortOrder;
use Magento\Framework\TestFramework\Unit\Helper\ObjectManager;
use PHPUnit\Framework\MockObject\MockObject;
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
    protected function setUp(): void
    {
        $this->objectManager = new ObjectManager($this);
        $this->sorting = $this->objectManager->getObject(Sorting::class);
    }

    /**
     * Test apply sorting with founded sort field.
     */
    public function testApplyWithFoundedField(): void
    {
        /** @var SearchCriteriaInterface|MockObject $searchCriteriaMock */
        $searchCriteriaMock = $this->getMockForAbstractClass(SearchCriteriaInterface::class);
        /** @var SearchParameters|MockObject $searchParameters */
        $searchParameters = $this->createMock(SearchParameters::class);
        $sortOrderMock = $this->createMock(SortOrder::class);
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
        /** @var SearchCriteriaInterface|MockObject $searchCriteriaMock */
        $searchCriteriaMock = $this->getMockForAbstractClass(SearchCriteriaInterface::class);
        /** @var SearchParameters|MockObject $searchParameters */
        $searchParameters = $this->createMock(SearchParameters::class);
        $sortOrderMock = $this->createMock(SortOrder::class);
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
