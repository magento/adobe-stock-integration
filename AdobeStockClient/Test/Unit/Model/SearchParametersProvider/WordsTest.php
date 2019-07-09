<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

declare(strict_types=1);

namespace AdobeStockClient\Test\Unit\Model\SearchParametersProvider;

use AdobeStock\Api\Exception\StockApi;
use AdobeStock\Api\Models\SearchParameters;
use Magento\AdobeStockClient\Model\SearchParametersProvider\Words;
use Magento\Framework\Api\Filter;
use Magento\Framework\Api\Search\FilterGroup;
use Magento\Framework\Api\SearchCriteriaInterface;
use Magento\Framework\Escaper;
use Magento\Framework\TestFramework\Unit\Helper\ObjectManager;
use PHPUnit\Framework\TestCase;

/**
 * Test that symbols " and \ will be removed from request.
 */
class WordsTest extends TestCase
{
    /**
     * @var ObjectManager
     */
    private $objectManager;

    /**
     * @var Escaper|\PHPUnit_Framework_MockObject_MockObject
     */
    private $escaperMock;

    /**
     * @var Words|\PHPUnit_Framework_MockObject_MockObject
     */
    private $words;

    /**
     * Prepare test objects.
     */
    public function setUp()
    {
        $this->objectManager = new ObjectManager($this);
        $this->escaperMock = $this->getMockBuilder(Escaper::class)
            ->setMethods(['encodeUrlParam'])
            ->disableOriginalConstructor()
            ->getMock();
        $this->words = $this->objectManager->getObject(
            Words::class,
            [
                'escaper' => $this->escaperMock
            ]
        );
    }

    /**
     * Check that quotes symbol will be deleted from request.
     *
     * @param string $requestValue
     * @param string $encodedValue
     * @throws StockApi
     * @dataProvider requestValuesDataProvider
     */
    public function testApplyWithRequestValue(string $requestValue, string $encodedValue)
    {
        /** @var SearchCriteriaInterface|\PHPUnit_Framework_MockObject_MockObject $searchCriteriaMock */
        $searchCriteriaMock = $this->getMockBuilder(SearchCriteriaInterface::class)
            ->setMethods(['getFilterGroups'])
            ->disableOriginalConstructor()
            ->getMockForAbstractClass();
        /** @var SearchParameters|\PHPUnit_Framework_MockObject_MockObject $searchParamsMock */
        $searchParamsMock = $this->getMockBuilder(SearchParameters::class)
            ->setMethods(['setWords'])
            ->disableOriginalConstructor()
            ->getMock();
        $filterGroupMock = $this->getMockBuilder(FilterGroup::class)
            ->setMethods(['getFilters'])
            ->disableOriginalConstructor()
            ->getMock();
        $searchCriteriaMock->expects($this->once())
            ->method('getFilterGroups')
            ->willReturn([$filterGroupMock]);
        $filter = $this->getMockBuilder(Filter::class)
            ->setMethods(['getField', 'getValue'])
            ->disableOriginalConstructor()
            ->getMock();
        $filter->expects($this->once())
            ->method('getField')
            ->willReturn('words');
        $filter->expects($this->exactly(2))
            ->method('getValue')
            ->willReturn($requestValue);
        $filterGroupMock->expects($this->once())
            ->method('getFilters')
            ->willReturn([$filter]);
        $this->escaperMock->expects($this->once())
            ->method('encodeUrlParam')
            ->with('Test query')
            ->willReturn('Test%20query');
        $searchParamsMock->expects($this->once())
            ->method('setWords')
            ->with($encodedValue);
        $methodResult = $this->words->apply($searchCriteriaMock, $searchParamsMock);
        $this->assertInstanceOf(SearchParameters::class, $methodResult);
    }

    /**
     * Check that words doesn't set to search params if filter by words is absent.
     *
     * @throws StockApi
     */
    public function testApplyWithoutWordsField()
    {
        /** @var SearchCriteriaInterface|\PHPUnit_Framework_MockObject_MockObject $searchCriteriaMock */
        $searchCriteriaMock = $this->getMockBuilder(SearchCriteriaInterface::class)
            ->setMethods(['getFilterGroups'])
            ->disableOriginalConstructor()
            ->getMockForAbstractClass();
        /** @var SearchParameters|\PHPUnit_Framework_MockObject_MockObject $searchParamsMock */
        $searchParamsMock = $this->getMockBuilder(SearchParameters::class)
            ->setMethods(['setWords'])
            ->disableOriginalConstructor()
            ->getMock();
        $filterGroupMock = $this->getMockBuilder(FilterGroup::class)
            ->setMethods(['getFilters'])
            ->disableOriginalConstructor()
            ->getMock();
        $searchCriteriaMock->expects($this->once())
            ->method('getFilterGroups')
            ->willReturn([$filterGroupMock]);
        $filter = $this->getMockBuilder(Filter::class)
            ->setMethods(['getField', 'getValue'])
            ->disableOriginalConstructor()
            ->getMock();
        $filter->expects($this->once())
            ->method('getField')
            ->willReturn('other_field');
        $filter->expects($this->never())
            ->method('getValue');
        $filterGroupMock->expects($this->once())
            ->method('getFilters')
            ->willReturn([$filter]);
        $this->escaperMock->expects($this->never())
            ->method('encodeUrlParam')
            ->with('Test query');
        $searchParamsMock->expects($this->never())
            ->method('setWords');
        $methodResult = $this->words->apply($searchCriteriaMock, $searchParamsMock);
        $this->assertInstanceOf(SearchParameters::class, $methodResult);
    }

    /**
     * Check that words doesn't set to search params if words is empty.
     *
     * @throws StockApi
     */
    public function testApplyWithEmptyWords()
    {
        /** @var SearchCriteriaInterface|\PHPUnit_Framework_MockObject_MockObject $searchCriteriaMock */
        $searchCriteriaMock = $this->getMockBuilder(SearchCriteriaInterface::class)
            ->setMethods(['getFilterGroups'])
            ->disableOriginalConstructor()
            ->getMockForAbstractClass();
        /** @var SearchParameters|\PHPUnit_Framework_MockObject_MockObject $searchParamsMock */
        $searchParamsMock = $this->getMockBuilder(SearchParameters::class)
            ->setMethods(['setWords'])
            ->disableOriginalConstructor()
            ->getMock();
        $filterGroupMock = $this->getMockBuilder(FilterGroup::class)
            ->setMethods(['getFilters'])
            ->disableOriginalConstructor()
            ->getMock();
        $searchCriteriaMock->expects($this->once())
            ->method('getFilterGroups')
            ->willReturn([$filterGroupMock]);
        $filter = $this->getMockBuilder(Filter::class)
            ->setMethods(['getField', 'getValue'])
            ->disableOriginalConstructor()
            ->getMock();
        $filter->expects($this->once())
            ->method('getField')
            ->willReturn('words');
        $filter->expects($this->once())
            ->method('getValue')
            ->willReturn('');
        $filterGroupMock->expects($this->once())
            ->method('getFilters')
            ->willReturn([$filter]);
        $this->escaperMock->expects($this->never())
            ->method('encodeUrlParam')
            ->with('Test query');
        $searchParamsMock->expects($this->never())
            ->method('setWords');
        $methodResult = $this->words->apply($searchCriteriaMock, $searchParamsMock);
        $this->assertInstanceOf(SearchParameters::class, $methodResult);
    }

    /**
     * Check that words doesn't set to search params if words is empty after replace unnecessary symbols.
     *
     * @throws StockApi
     */
    public function testApplyWithOnlyQuotes()
    {
        /** @var SearchCriteriaInterface|\PHPUnit_Framework_MockObject_MockObject $searchCriteriaMock */
        $searchCriteriaMock = $this->getMockBuilder(SearchCriteriaInterface::class)
            ->setMethods(['getFilterGroups'])
            ->disableOriginalConstructor()
            ->getMockForAbstractClass();
        /** @var SearchParameters|\PHPUnit_Framework_MockObject_MockObject $searchParamsMock */
        $searchParamsMock = $this->getMockBuilder(SearchParameters::class)
            ->setMethods(['setWords'])
            ->disableOriginalConstructor()
            ->getMock();
        $filterGroupMock = $this->getMockBuilder(FilterGroup::class)
            ->setMethods(['getFilters'])
            ->disableOriginalConstructor()
            ->getMock();
        $searchCriteriaMock->expects($this->once())
            ->method('getFilterGroups')
            ->willReturn([$filterGroupMock]);
        $filter = $this->getMockBuilder(Filter::class)
            ->setMethods(['getField', 'getValue'])
            ->disableOriginalConstructor()
            ->getMock();
        $filter->expects($this->once())
            ->method('getField')
            ->willReturn('words');
        $filter->expects($this->exactly(2))
            ->method('getValue')
            ->willReturn('"');
        $filterGroupMock->expects($this->once())
            ->method('getFilters')
            ->willReturn([$filter]);
        $this->escaperMock->expects($this->never())
            ->method('encodeUrlParam')
            ->with('Test query');
        $searchParamsMock->expects($this->never())
            ->method('setWords');
        $methodResult = $this->words->apply($searchCriteriaMock, $searchParamsMock);
        $this->assertInstanceOf(SearchParameters::class, $methodResult);
    }

    /**
     * Request values.
     *
     * @return array
     */
    public function requestValuesDataProvider(): array
    {
        return [
            [
                'requestValue' => 'Test "query"',
                'encodedValue' => 'Test%20query',
            ],
            [
                'requestValue' => 'Test \query\\',
                'encodedValue' => 'Test%20query',
            ],
            [
                'requestValue' => '"Test \query\\"',
                'encodedValue' => 'Test%20query',
            ],
        ];
    }
}
