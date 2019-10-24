<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Magento\AdobeStockClient\Test\Unit\Model\SearchParametersProvider;

use AdobeStock\Api\Models\SearchParameters;
use Magento\AdobeStockClient\Model\SearchParametersProvider\Orientation;
use Magento\Framework\Api\Filter;
use Magento\Framework\Api\Search\FilterGroup;
use Magento\Framework\Api\SearchCriteriaInterface;
use Magento\Framework\TestFramework\Unit\Helper\ObjectManager;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

/**
 * Test filter for images orientation: landscape, square, vertical, etc.
 */
class OrientationTest extends TestCase
{
    /**
     * @var ObjectManager
     */
    private $objectManager;

    /**
     * @var Orientation
     */
    private $orientation;

    /**
     * Prepare test objects.
     */
    public function setUp(): void
    {
        $this->objectManager = new ObjectManager($this);
        $this->orientation = $this->objectManager->getObject(Orientation::class);
    }

    /**
     * Test filter apply with panoramic value.
     */
    public function testApplyWithPanoramicValue(): void
    {
        /** @var SearchCriteriaInterface|MockObject $searchCriteriaMock */
        $searchCriteriaMock = $this->getMockBuilder(SearchCriteriaInterface::class)
            ->setMethods(['getFilterGroups'])
            ->disableOriginalConstructor()
            ->getMockForAbstractClass();
        /** @var SearchParameters|MockObject $searchParameters */
        $searchParameters = $this->getMockBuilder(SearchParameters::class)
            ->setMethods(['setFilterPanoromicOn', 'setOrientation'])
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
            ->willReturn('orientation_filter');
        $filterGroupItemMock->expects($this->once())
            ->method('getFilters')
            ->willReturn([$filterItemMock]);
        $searchCriteriaMock->expects($this->once())
            ->method('getFilterGroups')
            ->willReturn([$filterGroupItemMock]);
        $filterItemMock->expects($this->once())
            ->method('getValue')
            ->willReturn('PANORAMIC');
        $searchParameters->expects($this->once())
            ->method('setFilterPanoromicOn')
            ->with(true);
        $searchParameters->expects($this->never())
            ->method('setOrientation');
        $methodResult = $this->orientation->apply($searchCriteriaMock, $searchParameters);
        $this->assertInstanceOf(SearchParameters::class, $methodResult);
    }

    /**
     * Test filter apply without panoramic value.
     */
    public function testApplyWithoutPanoramicValue(): void
    {
        /** @var SearchCriteriaInterface|MockObject $searchCriteriaMock */
        $searchCriteriaMock = $this->getMockBuilder(SearchCriteriaInterface::class)
            ->setMethods(['getFilterGroups'])
            ->disableOriginalConstructor()
            ->getMockForAbstractClass();
        /** @var SearchParameters|MockObject $searchParameters */
        $searchParameters = $this->getMockBuilder(SearchParameters::class)
            ->setMethods(['setFilterPanoromicOn', 'setOrientation'])
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
            ->willReturn('orientation_filter');
        $filterGroupItemMock->expects($this->once())
            ->method('getFilters')
            ->willReturn([$filterItemMock]);
        $searchCriteriaMock->expects($this->once())
            ->method('getFilterGroups')
            ->willReturn([$filterGroupItemMock]);
        $filterItemMock->expects($this->exactly(2))
            ->method('getValue')
            ->willReturn('random_value');
        $searchParameters->expects($this->never())
            ->method('setFilterPanoromicOn');
        $searchParameters->expects($this->once())
            ->method('setOrientation')
            ->with('random_value');
        $methodResult = $this->orientation->apply($searchCriteriaMock, $searchParameters);
        $this->assertInstanceOf(SearchParameters::class, $methodResult);
    }
}
