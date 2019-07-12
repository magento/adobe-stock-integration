<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace AdobeStockClient\Test\Unit\Model\SearchParametersProvider;

use AdobeStock\Api\Models\SearchParameters;
use Magento\AdobeStockClient\Model\SearchParametersProvider\Pagination;
use Magento\Framework\Api\SearchCriteriaInterface;
use Magento\Framework\TestFramework\Unit\Helper\ObjectManager;
use PHPUnit\Framework\TestCase;

/**
 * Test handles pagination of search results.
 */
class PaginationTest extends TestCase
{
    /**
     * @var ObjectManager
     */
    private $objectManager;

    /**
     * @var Pagination
     */
    private $pagination;

    /**
     * Prepare test objects.
     */
    public function setUp(): void
    {
        $this->objectManager = new ObjectManager($this);
        $this->pagination = $this->objectManager->getObject(Pagination::class);
    }

    /**
     * Test filter apply.
     */
    public function testApply(): void
    {
        /** @var SearchCriteriaInterface|\PHPUnit_Framework_MockObject_MockObject $searchCriteriaMock */
        $searchCriteriaMock = $this->getMockBuilder(SearchCriteriaInterface::class)
            ->setMethods(['getPageSize', 'getCurrentPage'])
            ->disableOriginalConstructor()
            ->getMockForAbstractClass();
        $searchCriteriaMock->expects($this->exactly(2))
            ->method('getPageSize')
            ->willReturn(20);
        $searchCriteriaMock->expects($this->once())
            ->method('getCurrentPage')
            ->willReturn(1);
        /** @var SearchParameters|\PHPUnit_Framework_MockObject_MockObject $searchParameters */
        $searchParameters = $this->getMockBuilder(SearchParameters::class)
            ->setMethods(['setOffset', 'setLimit'])
            ->disableOriginalConstructor()
            ->getMock();
        $searchParameters->expects($this->once())
            ->method('setLimit')
            ->with(20);
        $searchParameters->expects($this->once())
            ->method('setOffset')
            ->with(0);
        $methodResult = $this->pagination->apply($searchCriteriaMock, $searchParameters);
        $this->assertInstanceOf(SearchParameters::class, $methodResult);
    }
}
