<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Magento\AdobeStockClient\Test\Unit\Model\SearchParametersProvider;

use AdobeStock\Api\Models\SearchParameters;
use Magento\AdobeStockClient\Model\SearchParametersProvider\SimpleFilters;
use Magento\Framework\Api\Filter;
use Magento\Framework\Api\Search\FilterGroup;
use Magento\Framework\Api\SearchCriteriaInterface;
use Magento\Framework\Escaper;
use Magento\Framework\TestFramework\Unit\Helper\ObjectManager;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

/**
 * Test apply filters that do not require additional business logic to search parameters.
 */
class SimpleFiltersTest extends TestCase
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
     * @var SimpleFilters
     */
    private $simpleFilters;

    /**
     * @var array
     */
    private $testFilters = [
        'premium_price_filter' => 'setFilterPremium',
        'words' => 'setWords',
        'colors_filter' => 'setFilterColors',
    ];

    /**
     * Prepare test objects.
     */
    protected function setUp(): void
    {
        $this->objectManager = new ObjectManager($this);
        $this->escaperMock = $this->createMock(Escaper::class);
        $this->simpleFilters = $this->objectManager->getObject(
            SimpleFilters::class,
            [
                'escaper' => $this->escaperMock,
                'filters' => $this->testFilters
            ]
        );
    }

    /**
     * Test filter apply.
     */
    public function testApply(): void
    {
        /** @var SearchCriteriaInterface|MockObject $searchCriteriaMock */
        $searchCriteriaMock = $this->createMock(SearchCriteriaInterface::class);
        /** @var SearchParameters|MockObject $searchParameters */
        $searchParameters = $this->createMock(SearchParameters::class);
        $filterGroupItemMock = $this->createMock(FilterGroup::class);
        $filterItemMock = $this->createMock(Filter::class);
        $filterItemMock->expects($this->exactly(2))
            ->method('getField')
            ->willReturn('words');
        $filterGroupItemMock->expects($this->once())
            ->method('getFilters')
            ->willReturn([$filterItemMock]);
        $searchCriteriaMock->expects($this->once())
            ->method('getFilterGroups')
            ->willReturn([$filterGroupItemMock]);
        $searchParameters->expects($this->once())
            ->method('setWords')
            ->with('value+1');
        $filterItemMock->expects($this->atLeastOnce())
            ->method('getValue')
            ->willReturn('value 1');
        $this->escaperMock->expects($this->once())
            ->method('encodeUrlParam')
            ->with('value 1')
            ->willReturn('value+1');
        $methodResult = $this->simpleFilters->apply($searchCriteriaMock, $searchParameters);
        $this->assertInstanceOf(SearchParameters::class, $methodResult);
    }
}
