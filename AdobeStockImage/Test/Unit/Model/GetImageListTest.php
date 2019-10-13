<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magento\AdobeStockImage\Test\Unit\Model;

use Magento\AdobeStockAssetApi\Api\GetAssetListInterface;
use Magento\AdobeStockImage\Model\GetImageList;
use Magento\Framework\Api\FilterBuilder;
use Magento\Framework\Api\Search\FilterGroupBuilder;
use Magento\Framework\Api\Search\SearchCriteriaInterface;
use Magento\Framework\Api\Search\SearchResultInterface;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\TestFramework\Unit\Helper\ObjectManager;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

/**
 * Test for Magento\AdobeStockImage\Model\GetImageList Model.
 */
class GetImageListTest extends TestCase
{
    /**
     * @var GetAssetListInterface|MockObject
     */
    private $getAssetListMock;

    /**
     * @var GetImageList
     */
    private $getImageListModel;

    /**
     * @var FilterGroupBuilder|MockObject
     */
    private $filterGroupBuilderMock;

    /**
     * @var FilterBuilder|MockObject
     */
    private $filterBuilderMock;

    /**
     * @inheritDoc
     */
    public function setUp()
    {
        $objectManager = new ObjectManager($this);
        $this->getAssetListMock = $this->createMock(GetAssetListInterface::class);
        $this->filterGroupBuilderMock = $this->createMock(FilterGroupBuilder::class);
        $this->filterBuilderMock = $this->createMock(FilterBuilder::class);

        $this->getImageListModel = $objectManager->getObject(
            GetImageList::class,
            [
                'getAssetList' => $this->getAssetListMock,
                'filterGroupBuilder' => $this->filterGroupBuilderMock,
                'filterBuilder' => $this->filterBuilderMock
            ]
        );
    }

    /**
     * Test 'execute' method of GetImageList class.
     * @throws LocalizedException
     */
    public function testExecute()
    {
        /** @var MockObject|SearchCriteriaInterface $searchCriteriaMock */
        $searchCriteriaMock = $this->createMock(SearchCriteriaInterface::class);
        $searchCriteriaMock
            ->method('getFilterGroups')
            ->willReturn([]);

        $searchResultMock = $this->createMock(SearchResultInterface::class);

        $this->getAssetListMock->expects($this->once())
            ->method('execute')
            ->with($searchCriteriaMock)
            ->willReturn($searchResultMock);

        $this->assertEquals($searchResultMock, $this->getImageListModel->execute($searchCriteriaMock));
    }
}
