<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

declare(strict_types=1);

namespace Magento\AdobeStockClient\Test\Unit\Model\SearchParametersProvider;

use AdobeStock\Api\Exception\StockApi;
use AdobeStock\Api\Models\SearchParameters;
use Magento\AdobeStockClient\Model\SearchParametersProvider\Words;
use Magento\Framework\Api\Filter;
use Magento\Framework\Api\Search\FilterGroup;
use Magento\Framework\Api\SearchCriteriaInterface;
use Magento\Framework\Escaper;
use Magento\Framework\TestFramework\Unit\Helper\ObjectManager;
use PHPUnit\Framework\MockObject\MockObject;
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
     * @var Escaper|MockObject
     */
    private $escaperMock;

    /**
     * @var Words|MockObject
     */
    private $words;

    /**
     * Prepare test objects.
     */
    protected function setUp(): void
    {
        $this->objectManager = new ObjectManager($this);
        $this->escaperMock = $this->createMock(Escaper::class);
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
    public function testApplyWithRequestValue(string $requestValue, string $encodedValue): void
    {
        /** @var SearchCriteriaInterface|MockObject $searchCriteriaMock */
        $searchCriteriaMock = $this->getMockForAbstractClass(SearchCriteriaInterface::class);
        /** @var SearchParameters|MockObject $searchParamsMock */
        $searchParamsMock = $this->createMock(SearchParameters::class);
        $filterGroupMock = $this->createMock(FilterGroup::class);
        $searchCriteriaMock->expects($this->once())
            ->method('getFilterGroups')
            ->willReturn([$filterGroupMock]);
        $filter = $this->createMock(Filter::class);
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
    public function testApplyWithoutWordsField(): void
    {
        /** @var SearchCriteriaInterface|MockObject $searchCriteriaMock */
        $searchCriteriaMock = $this->getMockForAbstractClass(SearchCriteriaInterface::class);
        /** @var SearchParameters|MockObject $searchParamsMock */
        $searchParamsMock = $this->createMock(SearchParameters::class);
        $filterGroupMock = $this->createMock(FilterGroup::class);
        $searchCriteriaMock->expects($this->once())
            ->method('getFilterGroups')
            ->willReturn([$filterGroupMock]);
        $filter = $this->createMock(Filter::class);
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
    public function testApplyWithEmptyWords(): void
    {
        /** @var SearchCriteriaInterface|MockObject $searchCriteriaMock */
        $searchCriteriaMock = $this->getMockForAbstractClass(SearchCriteriaInterface::class);
        /** @var SearchParameters|MockObject $searchParamsMock */
        $searchParamsMock = $this->createMock(SearchParameters::class);
        $filterGroupMock = $this->createMock(FilterGroup::class);
        $searchCriteriaMock->expects($this->once())
            ->method('getFilterGroups')
            ->willReturn([$filterGroupMock]);
        $filter = $this->createMock(Filter::class);
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
    public function testApplyWithOnlyQuotes(): void
    {
        /** @var SearchCriteriaInterface|MockObject $searchCriteriaMock */
        $searchCriteriaMock = $this->getMockForAbstractClass(SearchCriteriaInterface::class);
        /** @var SearchParameters|MockObject $searchParamsMock */
        $searchParamsMock = $this->createMock(SearchParameters::class);
        $filterGroupMock = $this->createMock(FilterGroup::class);
        $searchCriteriaMock->expects($this->once())
            ->method('getFilterGroups')
            ->willReturn([$filterGroupMock]);
        $filter = $this->createMock(Filter::class);
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
