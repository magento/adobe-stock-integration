<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Magento\AdobeStockClient\Test\Unit\Model\SearchParametersProvider;

use AdobeStock\Api\Models\SearchParameters;
use Magento\AdobeStockClient\Model\SearchParametersProvider\Premium;
use Magento\Framework\Api\Filter;
use Magento\Framework\Api\Search\FilterGroup;
use Magento\Framework\Api\SearchCriteriaInterface;
use Magento\Framework\TestFramework\Unit\Helper\ObjectManager;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

/**
 * Test Premium filter.
 */
class PremiumTest extends TestCase
{
    private const FILTER_TYPE = 'premium_price_filter';

    /**
     * @var ObjectManager
     */
    private $objectManager;

    /**
     * @var Premium
     */
    private $sut;

    /**
     * @var SearchCriteriaInterface|MockObject
     */
    private $searchCriteriaMock;

    /**
     * @var SearchParameters|MockObject
     */
    private $searchParametersMock;

    /**
     * @var FilterGroup|MockObject
     */
    private $filterGroupItemMock;

    /**
     * @var Filter|MockObject
     */
    private $filterItemMock;

    /**
     * Prepare test objects.
     */
    protected function setUp(): void
    {
        $this->objectManager = new ObjectManager($this);
        $this->searchCriteriaMock = $this->getMockForAbstractClass(SearchCriteriaInterface::class);
        $this->searchParametersMock = $this->createMock(SearchParameters::class);
        $this->filterGroupItemMock = $this->createMock(FilterGroup::class);
        $this->filterItemMock = $this->createMock(Filter::class);

        $this->sut = $this->objectManager->getObject(Premium::class);
    }

    /**
     * Test filter apply
     *
     * @param string $filterName
     * @param string $filterValue
     *
     * @dataProvider filterTypesDataProvider
     */
    public function testApply(string $filterName, string $filterValue): void
    {
        $invokedTimes = (int) ($filterName === self::FILTER_TYPE);
        $this->filterItemMock->expects($this->once())
            ->method('getField')
            ->willReturn($filterName);
        $this->filterItemMock->expects($this->exactly($invokedTimes))
            ->method('getValue')
            ->willReturn($filterValue);
        $this->filterGroupItemMock->expects($this->once())
            ->method('getFilters')
            ->willReturn([$this->filterItemMock]);
        $this->searchParametersMock->expects($this->exactly($invokedTimes))
            ->method('setFilterPremium')
            ->with($filterValue);
        $this->searchCriteriaMock->expects($this->once())
            ->method('getFilterGroups')
            ->willReturn([$this->filterGroupItemMock]);

        $methodResult = $this->sut->apply($this->searchCriteriaMock, $this->searchParametersMock);
        $this->assertInstanceOf(SearchParameters::class, $methodResult);
    }

    /**
     * Providing filter types
     *
     * @return array
     */
    public function filterTypesDataProvider(): array
    {
        return [
            [
                'name' => self::FILTER_TYPE,
                'value' => '123'
            ], [
                'name' => 'offensive_filter',
                'value' => '456'
            ]
        ];
    }
}
