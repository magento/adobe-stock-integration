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
     * @var Escaper|\PHPUnit_Framework_MockObject_MockObject
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
    public function setUp(): void
    {
        $this->objectManager = new ObjectManager($this);
        $this->escaperMock = $this->getMockBuilder(Escaper::class)
            ->setMethods(['encodeUrlParam'])
            ->disableOriginalConstructor()
            ->getMock();
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
        /** @var SearchCriteriaInterface|\PHPUnit_Framework_MockObject_MockObject $searchCriteriaMock */
        $searchCriteriaMock = $this->getMockBuilder(SearchCriteriaInterface::class)
            ->setMethods(['getFilterGroups'])
            ->disableOriginalConstructor()
            ->getMockForAbstractClass();
        /** @var SearchParameters|\PHPUnit_Framework_MockObject_MockObject $searchParameters */
        $searchParameters = $this->getMockBuilder(SearchParameters::class)
            ->setMethods(['setOrder', 'setWords'])
            ->disableOriginalConstructor()
            ->getMock();
        $filterGroupItemMock = $this->getMockBuilder(FilterGroup::class)
            ->setMethods(['getFilters'])
            ->disableOriginalConstructor()
            ->getMock();
        $filterItemMock = $this->getMockBuilder(Filter::class)
            ->setMethods(['getField', 'getValue'])
            ->disableOriginalConstructor()
            ->getMock();
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
