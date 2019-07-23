<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

declare(strict_types=1);

namespace Magento\AdobeStockImage\Test\Unit\Plugin\Product\Gallery;

/**
 * Test for the Gallery Processor Plugin (ensures that metadata is remove from the database when image is deleted)
 */
class ProcessorTest extends \PHPUnit\Framework\TestCase
{
    /**
     * @var \Magento\AdobeStockImage\Plugin\Product\Gallery\Processor
     */
    private $model;

    /**
     * @var \PHPUnit\Framework\MockObject\MockObject
     */
    private $assetRepositoryMock;

    /**
     * @var \PHPUnit\Framework\MockObject\MockObject
     */
    private $filterBuilderMock;

    /**
     * @var \PHPUnit\Framework\MockObject\MockObject
     */
    private $searchCriteriaBuilderMock;

    /**
     * @inheritdoc
     */
    protected function setUp()
    {
        parent::setUp();
        $this->assetRepositoryMock = $this->createMock(\Magento\AdobeStockAssetApi\Api\AssetRepositoryInterface::class);
        $this->filterBuilderMock = $this->createMock(\Magento\Framework\Api\FilterBuilder::class);
        $this->searchCriteriaBuilderMock = $this->createMock(
            \Magento\Framework\Api\SearchCriteriaBuilderFactory::class
        );
        $this->model = new \Magento\AdobeStockImage\Plugin\Product\Gallery\Processor(
            $this->assetRepositoryMock,
            $this->filterBuilderMock,
            $this->searchCriteriaBuilderMock
        );
    }

    public function testAfterRemoveImageIfPathIsNull()
    {
        $resultMock = $this->createMock(\Magento\Catalog\Model\Product\Gallery\Processor::class);
        $result = $this->model->afterRemoveImage(
            $this->createMock(\Magento\Catalog\Model\Product\Gallery\Processor::class),
            $resultMock,
            $this->createMock(\Magento\Catalog\Model\Product::class),
            null
        );

        $this->assertEquals($result, $resultMock);
    }

    public function testAfterRemoveImage()
    {
        $filePath = 'file/path';
        $resultMock = $this->createMock(\Magento\Catalog\Model\Product\Gallery\Processor::class);
        $productMock = $this->createMock(\Magento\Catalog\Model\Product::class);

        $filterMock = $this->createMock(\Magento\Framework\Api\Filter::class);
        $this->filterBuilderMock->method('setField')->with('path')->willReturnSelf();
        $this->filterBuilderMock->method('setConditionType')->with('eq')->willReturnSelf();
        $this->filterBuilderMock->method('setValue')->with($filePath)->willReturnSelf();
        $this->filterBuilderMock->method('create')->willReturn($filterMock);

        $searchCriteriaBuilder = $this->createMock(\Magento\Framework\Api\SearchCriteriaBuilder::class);
        $this->searchCriteriaBuilderMock->expects($this->once())->method('create')->willReturn($searchCriteriaBuilder);
        $searchCriteriaBuilder->expects($this->once())->method('addFilters')->with([$filterMock])->willReturnSelf();

        $searchCriteriaMock = $this->createMock(\Magento\Framework\Api\SearchCriteria::class);
        $searchCriteriaBuilder->expects($this->once())->method('create')->willReturn($searchCriteriaMock);

        $assetMock = $this->createMock(\Magento\AdobeStockAssetApi\Api\Data\AssetInterface::class);
        $searchResultMock = $this->createMock(\Magento\AdobeStockAssetApi\Api\Data\AssetSearchResultsInterface::class);
        $searchResultMock->expects($this->once())->method('getItems')->willReturn([$assetMock]);

        $this->assetRepositoryMock->expects($this->once())
            ->method('getList')
            ->with($searchCriteriaMock)
            ->willReturn($searchResultMock);

        $this->assetRepositoryMock->expects($this->once())
            ->method('delete')
            ->with($assetMock);

        $result = $this->model->afterRemoveImage(
            $this->createMock(\Magento\Catalog\Model\Product\Gallery\Processor::class),
            $resultMock,
            $productMock,
            $filePath
        );

        $this->assertEquals($result, $resultMock);
    }
}
