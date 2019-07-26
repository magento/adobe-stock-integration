<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

declare(strict_types=1);

namespace Magento\AdobeStockImage\Test\Unit\Model;

use Magento\AdobeStockAssetApi\Api\Data\AssetInterface;
use Magento\AdobeStockAssetApi\Api\Data\AssetInterfaceFactory;
use Magento\AdobeStockAssetApi\Api\Data\AssetSearchResultsInterface;
use Magento\AdobeStockAssetApi\Api\Data\AssetSearchResultsInterfaceFactory;
use Magento\AdobeStockClientApi\Api\ClientInterface;
use Magento\AdobeStockImage\Model\GetImageList;
use Magento\Framework\Api\AttributeInterface;
use Magento\Framework\Api\Search\DocumentInterface;
use Magento\Framework\Api\Search\SearchResultInterface;
use Magento\Framework\Api\SearchCriteriaInterface;
use Magento\Framework\TestFramework\Unit\Helper\ObjectManager;
use Magento\Framework\UrlInterface;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

/**
 * Test for GetImageList service
 */
class GetImageListTest extends TestCase
{
    /**
     * @var GetImageList
     */
    private $model;

    /**
     * @var ClientInterface|MockObject
     */
    private $clientMock;

    /**
     * @var AssetInterfaceFactory|MockObject
     */
    private $assetFactoryMock;

    /**
     * @var AssetSearchResultsInterfaceFactory|MockObject
     */
    private $searchResultFactoryMock;

    /**
     * @var UrlInterface|MockObject
     */
    private $urlMock;

    /**
     * @inheritdoc
     */
    protected function setUp()
    {
        $this->clientMock = $this->createMock(ClientInterface::class);
        $this->assetFactoryMock = $this->createMock(AssetInterfaceFactory::class);
        $this->searchResultFactoryMock = $this->createMock(AssetSearchResultsInterfaceFactory::class);
        $this->urlMock = $this->createMock(UrlInterface::class);

        $this->model = (new ObjectManager($this))->getObject(
            GetImageList::class,
            [
                'client'              => $this->clientMock,
                'assetFactory'        => $this->assetFactoryMock,
                'searchResultFactory' => $this->searchResultFactoryMock,
                'url'                 => $this->urlMock,
            ]
        );
    }

    /**
     * Test execute method
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function testExecute()
    {
        $id = 1;
        $thumbnailUrl = 'url.com/test/';
        $previewUrl = 'url.com/test/';
        $height = 100;
        $width = 200;

        $searchCriteriaMock = $this->createMock(SearchCriteriaInterface::class);

        $documentMock = $this->getDocument($id, $thumbnailUrl, $previewUrl, $height, $width);

        $searchResultMock = $this->createMock(SearchResultInterface::class);
        $searchResultMock->expects($this->once())->method('getItems')->willReturn([$documentMock]);
        $searchResultMock->expects($this->once())->method('getTotalCount')->willReturn(1);

        $this->clientMock->expects($this->once())
            ->method('search')
            ->with($searchCriteriaMock)
            ->willReturn($searchResultMock);

        $assetMock = $this->getAsset($id, $thumbnailUrl, $previewUrl, $height, $width);

        $this->assetFactoryMock->expects($this->once())->method('create')->willReturn($assetMock);

        $searchResultsMock = $this->createMock(AssetSearchResultsInterface::class);
        $this->searchResultFactoryMock->expects($this->once())
            ->method('create')
            ->with(
                [
                    'data' => [
                        'items'       => [$assetMock],
                        'total_count' => 1,
                    ],
                ]
            )
            ->willReturn($searchResultsMock);
        $this->assertEquals($this->model->execute($searchCriteriaMock), $searchResultsMock);
    }

    /**
     * @param int    $id
     * @param string $thumbnailUrl
     * @param string $previewUrl
     * @param int    $height
     * @param int    $width
     * @return DocumentInterface|MockObject
     */
    private function getDocument(
        int $id,
        string $thumbnailUrl,
        string $previewUrl,
        int $height,
        int $width
    ): DocumentInterface {
        $documentMock = $this->createMock(DocumentInterface::class);
        $documentMock->expects($this->once())->method('getId')->willReturn($id);
        $documentMock->method('getCustomAttribute')
            ->willReturnMap(
                [
                    ['thumbnail_url', $this->getAttribute($thumbnailUrl)],
                    ['preview_url', $this->getAttribute($previewUrl)],
                    ['height', $this->getAttribute($height)],
                    ['width', $this->getAttribute($width)],
                ]
            );

        return $documentMock;
    }

    /**
     * @param mixed $value
     * @return AttributeInterface|MockObject
     */
    private function getAttribute($value): AttributeInterface
    {
        $attribute = $this->createMock(AttributeInterface::class);
        $attribute->expects($this->once())->method('getValue')->willReturn($value);
        return $attribute;
    }

    /**
     * @param int    $id
     * @param string $thumbnailUrl
     * @param string $previewUrl
     * @param int    $height
     * @param int    $width
     * @return AssetInterface|MockObject
     */
    private function getAsset(
        int $id,
        string $thumbnailUrl,
        string $previewUrl,
        int $height,
        int $width
    ): AssetInterface {
        $assetMock = $this->createMock(AssetInterface::class);
        $assetMock->expects($this->once())->method('setId')->with($id);
        $assetMock->expects($this->once())->method('setThumbnailUrl')->with($thumbnailUrl);
        $assetMock->expects($this->once())->method('setPreviewUrl')->with($previewUrl);
        $assetMock->expects($this->once())->method('setHeight')->with($height);
        $assetMock->expects($this->once())->method('setWidth')->with($width);

        return $assetMock;
    }
}
