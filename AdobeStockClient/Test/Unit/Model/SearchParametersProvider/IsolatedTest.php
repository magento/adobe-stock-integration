<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace AdobeStockClient\Test\Unit\Model\SearchParametersProvider;

use AdobeStock\Api\Models\SearchParameters;
use Magento\AdobeStockClient\Model\SearchParametersProvider\Isolated;
use Magento\Framework\Api\Filter;
use Magento\Framework\Api\Search\FilterGroup;
use Magento\Framework\Api\SearchCriteriaInterface;
use Magento\Framework\TestFramework\Unit\Helper\ObjectManager;
use PHPUnit\Framework\TestCase;

/**
 * Test is image separated from (and by) background color.
 */
class IsolatedTest extends TestCase
{
    /**
     * @var ObjectManager
     */
    private $objectManager;

    /**
     * @var Isolated
     */
    private $isolated;

    /**
     * Prepare test objects.
     */
    public function setUp(): void
    {
        $this->objectManager = new ObjectManager($this);
        $this->isolated = $this->objectManager->getObject(Isolated::class);
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
            ->setMethods(['setFilterIsolatedOn'])
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
        $filterItemMock->expects($this->once())
            ->method('getField')
            ->willReturn('isolated_filter');
        $filterGroupItemMock->expects($this->once())
            ->method('getFilters')
            ->willReturn([$filterItemMock]);
        $searchCriteriaMock->expects($this->once())
            ->method('getFilterGroups')
            ->willReturn([$filterGroupItemMock]);
        $filterItemMock->expects($this->once())
            ->method('getValue')
            ->willReturn('Isolated Only');
        $searchParameters->expects($this->once())
            ->method('setFilterIsolatedOn')
            ->with(true);
        $methodResult = $this->isolated->apply($searchCriteriaMock, $searchParameters);
        $this->assertInstanceOf(SearchParameters::class, $methodResult);
    }
}
