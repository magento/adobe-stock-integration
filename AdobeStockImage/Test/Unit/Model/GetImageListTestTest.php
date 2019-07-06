<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magento\AdobeStockImage\Test\Unit\Model;

/**
 * Test for GetImageList service
 */
class GetImageListTestTest extends \PHPUnit\Framework\TestCase
{
    /**
     * @var \Magento\AdobeStockImage\Model\GetImageList
     */
    private $model;

    /**
     * @var \PHPUnit\Framework\MockObject\MockObject
     */
    private $clientMock;

    /**
     * @var \PHPUnit\Framework\MockObject\MockObject
     */
    private $assetFactoryMock;

    /**
     * @var \PHPUnit\Framework\MockObject\MockObject
     */
    private $searchResultFactoryMock;

    /**
     * @inheritdoc
     */
    protected function setUp()
    {
        parent::setUp();
        $this->clientMock = $this->createMock(\Magento\AdobeStockClientApi\Api\ClientInterface::class);
        $this->assetFactoryMock = $this->createMock(\Magento\AdobeStockAssetApi\Api\Data\AssetInterfaceFactory::class);
        $this->searchResultFactoryMock = $this->createMock(
            \Magento\AdobeStockAssetApi\Api\Data\AssetSearchResultsInterfaceFactory::class
        );
        $this->model = new \Magento\AdobeStockImage\Model\GetImageList(
            $this->clientMock,
            $this->assetFactoryMock,
            $this->searchResultFactoryMock
        );
    }

    /**
     * Test execute method
     */
    public function testExecute()
    {
        $id = 1;
        $url = 'url.com/test/';
        $height = 100;
        $width = 200;

        $searchCriteriaMock = $this->createMock(\Magento\Framework\Api\SearchCriteriaInterface::class);
        $searchResultMock = $this->createMock(\Magento\Framework\Api\Search\SearchResultInterface::class);

        $this->clientMock->expects($this->once())
            ->method('search')
            ->with($searchCriteriaMock)
            ->willReturn($searchResultMock);

        $urlAttr = $this->createMock(\Magento\Framework\Api\AttributeInterface::class);
        $urlAttr->expects($this->once())->method('getValue')->willReturn($url);
        $heightAttr = $this->createMock(\Magento\Framework\Api\AttributeInterface::class);
        $heightAttr->expects($this->once())->method('getValue')->willReturn($height);
        $widthAttr = $this->createMock(\Magento\Framework\Api\AttributeInterface::class);
        $widthAttr->expects($this->once())->method('getValue')->willReturn($width);

        $documentMock = $this->createMock(\Magento\Framework\Api\Search\DocumentInterface::class);
        $documentMock->expects($this->at(0))->method('getId')->willReturn($id);
        $documentMock->expects($this->at(1))->method('getCustomAttribute')->with('url')->willReturn($urlAttr);
        $documentMock->expects($this->at(2))->method('getCustomAttribute')->with('height')->willReturn($heightAttr);
        $documentMock->expects($this->at(3))->method('getCustomAttribute')->with('width')->willReturn($widthAttr);

        $searchResultMock->expects($this->once())->method('getItems')->willReturn([$documentMock]);
        $searchResultMock->expects($this->once())->method('getTotalCount')->willReturn(1);

        $assetMock = $this->createMock(\Magento\AdobeStockAssetApi\Api\Data\AssetInterface::class);
        $this->assetFactoryMock->expects($this->once())->method('create')->willReturn($assetMock);
        $assetMock->expects($this->once())->method('setId')->with($id)->willReturnSelf();
        $assetMock->expects($this->once())->method('setUrl')->with($url)->willReturnSelf();
        $assetMock->expects($this->once())->method('setHeight')->with($height)->willReturnSelf();
        $assetMock->expects($this->once())->method('setWidth')->with($width)->willReturnSelf();

        $searchResultsMock = $this->createMock(\Magento\AdobeStockAssetApi\Api\Data\AssetSearchResultsInterface::class);
        $this->searchResultFactoryMock->expects($this->once())
            ->method('create')
            ->with(
                [
                    'data' => [
                        'items' => [$assetMock],
                        'total_count' => 1
                    ]
                ]
            )
            ->willReturn($searchResultsMock);
        $this->assertEquals($this->model->execute($searchCriteriaMock), $searchResultsMock);
    }
}
