<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

declare(strict_types=1);

namespace Magento\AdobeStockImage\Test\Unit\Plugin\Product\Gallery;

use Magento\Framework\Api\SearchCriteriaBuilderFactory;
use Magento\AdobeStockAssetApi\Api\AssetRepositoryInterface;
use PHPUnit\Framework\MockObject\MockObject;
use Psr\Log\LoggerInterface;

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

    /** @var \Magento\Framework\Api\SearchCriteriaBuilder|MockObject */
    protected $searchCriteriaBuilderMock;

    /** @var  LoggerInterface|MockObject */
    private $loggerMock;

    /**
     * @inheritdoc
     */
    protected function setUp()
    {
        parent::setUp();
        $this->assetRepositoryMock = $this->createMock(
            AssetRepositoryInterface::class
        );
        $this->searchCriteriaBuilderMock = $this->createMock(
            SearchCriteriaBuilderFactory::class
        );
        $this->loggerMock = $this->createMock(LoggerInterface::class);

        $this->model = new \Magento\AdobeStockImage\Plugin\Product\Gallery\Processor(
            $this->assetRepositoryMock,
            $this->searchCriteriaBuilderMock,
            $this->loggerMock
        );
    }

    /**
     * Test afterRemoveImage if file path is null.
     */
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

    /**
     * Test successful afterRemoveImage method.
     *
     * @throws \ReflectionException
     */
    public function testSuccessfulAfterRemoveImage()
    {
        $filePath = 'file/path';
        $this->setupSuccessAssetSearchAndDelete();
        $resultMock = $this->createMock(\Magento\Catalog\Model\Product\Gallery\Processor::class);
        $productMock = $this->createMock(\Magento\Catalog\Model\Product::class);
        $result = $this->model->afterRemoveImage(
            $this->createMock(\Magento\Catalog\Model\Product\Gallery\Processor::class),
            $resultMock,
            $productMock,
            $filePath
        );

        $this->assertEquals($result, $resultMock);
    }

    /**
     * Test failed getList AssertRepository scenario.
     *
     * @throws \ReflectionException
     */
    public function testFailedAfterRemoveImage()
    {
        $filePath = 'file/path';
        $this->setupFailedAssetSearchAndDelete();
        $resultMock = $this->createMock(\Magento\Catalog\Model\Product\Gallery\Processor::class);
        $productMock = $this->createMock(\Magento\Catalog\Model\Product::class);
        $result = $this->model->afterRemoveImage(
            $this->createMock(\Magento\Catalog\Model\Product\Gallery\Processor::class),
            $resultMock,
            $productMock,
            $filePath
        );

        $this->assertEquals($result, $resultMock);
    }

    /**
     * Setup the successful asset search and delete mock.
     *
     * @throws \ReflectionException
     */
    private function setupSuccessAssetSearchAndDelete(): void
    {
        $id = 42;

        $searchCriteriaMock = $this->getSearchCriteriaMock();
        $assetMock = $this->createMock(\Magento\AdobeStockAssetApi\Api\Data\AssetInterface::class);
        $assetMock->expects($this->once())
            ->method('getId')
            ->willReturn($id);
        $searchResultMock = $this->createMock(\Magento\AdobeStockAssetApi\Api\Data\AssetSearchResultsInterface::class);
        $searchResultMock->expects($this->once())->method('getItems')->willReturn([$assetMock]);
        $this->assetRepositoryMock->expects($this->once())
            ->method('getList')
            ->with($searchCriteriaMock)
            ->willReturn($searchResultMock);

        $this->assetRepositoryMock->expects($this->once())
            ->method('deleteById')
            ->with($id);
    }

    /**
     * Setup the failed asset search and delete mock.
     *
     * @throws \ReflectionException
     */
    private function setupFailedAssetSearchAndDelete(): void
    {
        $searchCriteriaMock = $this->getSearchCriteriaMock();
        $this->assetRepositoryMock->expects($this->once())
            ->method('getList')
            ->with($searchCriteriaMock)
            ->willThrowException(new \Exception('Test error text'));
    }

    /**
     * Get search criteria mock object.
     *
     * @return \PHPUnit\Framework\MockObject\MockObject
     * @throws \ReflectionException
     */
    private function getSearchCriteriaMock()
    {
        $filePath = 'file/path';
        $searchCriteriaBuilder = $this->createMock(\Magento\Framework\Api\SearchCriteriaBuilder::class);
        $this->searchCriteriaBuilderMock->expects($this->once())->method('create')->willReturn($searchCriteriaBuilder);

        $searchCriteriaBuilder->expects($this->once())
            ->method('addFilter')
            ->with('path', $filePath)
            ->willReturnSelf();

        $searchCriteriaMock = $this->createMock(\Magento\Framework\Api\SearchCriteria::class);
        $searchCriteriaBuilder->expects($this->once())->method('create')->willReturn($searchCriteriaMock);

        return $searchCriteriaMock;
    }
}
