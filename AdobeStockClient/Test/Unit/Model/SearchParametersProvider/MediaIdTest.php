<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Magento\AdobeStockClient\Test\Unit\Model\SearchParametersProvider;

use AdobeStock\Api\Models\SearchParameters;
use Magento\AdobeStockClient\Model\SearchParametersProvider\MediaId;
use Magento\Framework\Api\Filter;
use Magento\Framework\Api\Search\FilterGroup;
use Magento\Framework\Api\SearchCriteriaInterface;
use Magento\Framework\TestFramework\Unit\Helper\ObjectManager;
use PHPUnit\Framework\TestCase;

/**
 * Test Adobe Media Id filter.
 */
class MediaIdTest extends TestCase
{
    /**
     * @var ObjectManager
     */
    private $objectManager;

    /**
     * @var MediaId
     */
    private $mediaId;

    /**
     * Prepare test objects.
     */
    public function setUp(): void
    {
        $this->objectManager = new ObjectManager($this);
        $this->mediaId = $this->objectManager->getObject(MediaId::class);
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
            ->setMethods(['setMediaId'])
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
            ->willReturn('media_id');
        $filterGroupItemMock->expects($this->once())
            ->method('getFilters')
            ->willReturn([$filterItemMock]);
        $searchCriteriaMock->expects($this->once())
            ->method('getFilterGroups')
            ->willReturn([$filterGroupItemMock]);
        $filterItemMock->expects($this->once())
            ->method('getValue')
            ->willReturn('12345');
        $searchParameters->expects($this->once())
            ->method('setMediaId')
            ->with(12345);
        $methodResult = $this->mediaId->apply($searchCriteriaMock, $searchParameters);
        $this->assertInstanceOf(SearchParameters::class, $methodResult);
    }
}
