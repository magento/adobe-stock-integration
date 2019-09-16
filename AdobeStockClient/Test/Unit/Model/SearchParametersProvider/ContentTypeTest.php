<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Magento\AdobeStockClient\Test\Unit\Model\SearchParametersProvider;

use AdobeStock\Api\Models\SearchParameters;
use Magento\AdobeStockClient\Model\SearchParametersProvider\ContentType;
use Magento\Framework\Api\Filter;
use Magento\Framework\Api\Search\FilterGroup;
use Magento\Framework\Api\SearchCriteriaInterface;
use Magento\Framework\TestFramework\Unit\Helper\ObjectManager;
use PHPUnit\Framework\TestCase;

/**
 * Test is image separated from (and by) background color.
 */
class ContentTypeTest extends TestCase
{
    /**
     * @var ObjectManager
     */
    private $objectManager;

    /**
     * @var ContentType
     */
    private $contentType;

    /**
     * Prepare test objects.
     */
    public function setUp(): void
    {
        $this->objectManager = new ObjectManager($this);
        $this->contentType = $this->objectManager->getObject(ContentType::class);
    }

    /**
     * Test filter apply with value "photo".
     */
    public function testApplyWithValuePhoto(): void
    {
        /** @var SearchCriteriaInterface|\PHPUnit_Framework_MockObject_MockObject $searchCriteriaMock */
        $searchCriteriaMock = $this->getMockBuilder(SearchCriteriaInterface::class)
            ->setMethods(['getFilterGroups'])
            ->disableOriginalConstructor()
            ->getMockForAbstractClass();
        /** @var SearchParameters|\PHPUnit_Framework_MockObject_MockObject $searchParameters */
        $searchParameters = $this->getMockBuilder(SearchParameters::class)
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
            ->willReturn('content_type_filter');
        $filterGroupItemMock->expects($this->once())
            ->method('getFilters')
            ->willReturn([$filterItemMock]);
        $searchCriteriaMock->expects($this->once())
            ->method('getFilterGroups')
            ->willReturn([$filterGroupItemMock]);
        $filterItemMock->expects($this->once())
            ->method('getValue')
            ->willReturn('photo');
        $methodResult = $this->contentType->apply($searchCriteriaMock, $searchParameters);
        $this->assertInstanceOf(SearchParameters::class, $methodResult);
    }

    /**
     * Test filter apply with value "illustration".
     */
    public function testApplyWithValueIllustration(): void
    {
        /** @var SearchCriteriaInterface|\PHPUnit_Framework_MockObject_MockObject $searchCriteriaMock */
        $searchCriteriaMock = $this->getMockBuilder(SearchCriteriaInterface::class)
            ->setMethods(['getFilterGroups'])
            ->disableOriginalConstructor()
            ->getMockForAbstractClass();
        /** @var SearchParameters|\PHPUnit_Framework_MockObject_MockObject $searchParameters */
        $searchParameters = $this->getMockBuilder(SearchParameters::class)->disableOriginalConstructor()
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
            ->willReturn('content_type_filter');
        $filterGroupItemMock->expects($this->once())
            ->method('getFilters')
            ->willReturn([$filterItemMock]);
        $searchCriteriaMock->expects($this->once())
            ->method('getFilterGroups')
            ->willReturn([$filterGroupItemMock]);
        $filterItemMock->expects($this->once())
            ->method('getValue')
            ->willReturn('illustration');
        $methodResult = $this->contentType->apply($searchCriteriaMock, $searchParameters);
        $this->assertInstanceOf(SearchParameters::class, $methodResult);
    }
}
