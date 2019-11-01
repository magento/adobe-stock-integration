<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Magento\AdobeStockClient\Test\Unit\Model\SearchParametersProvider;

use AdobeStock\Api\Models\SearchParameters;
use Magento\AdobeStockClient\Model\SearchParametersProvider\Offensive;
use Magento\Framework\Api\Filter;
use Magento\Framework\Api\Search\FilterGroup;
use Magento\Framework\Api\SearchCriteriaInterface;
use Magento\Framework\TestFramework\Unit\Helper\ObjectManager;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

/**
 * Test defines if offensive images should be excluded from the search results.
 */
class OffensiveTest extends TestCase
{
    /**
     * @var ObjectManager
     */
    private $objectManager;

    /**
     * @var Offensive
     */
    private $offensive;

    /**
     * Prepare test objects.
     */
    protected function setUp(): void
    {
        $this->objectManager = new ObjectManager($this);
        $this->offensive = $this->objectManager->getObject(Offensive::class);
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
        $filterItemMock->expects($this->once())
            ->method('getField')
            ->willReturn('offensive_filter');
        $filterGroupItemMock->expects($this->once())
            ->method('getFilters')
            ->willReturn([$filterItemMock]);
        $searchCriteriaMock->expects($this->once())
            ->method('getFilterGroups')
            ->willReturn([$filterGroupItemMock]);
        $filterItemMock->expects($this->once())
            ->method('getValue')
            ->willReturn('Disabled');
        $searchParameters->expects($this->once())
            ->method('setFilterOffensive2')
            ->with(true);
        $methodResult = $this->offensive->apply($searchCriteriaMock, $searchParameters);
        $this->assertInstanceOf(SearchParameters::class, $methodResult);
    }
}
