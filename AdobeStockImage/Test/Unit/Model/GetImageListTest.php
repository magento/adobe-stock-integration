<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Magento\AdobeStockImage\Test\Unit\Model;

use Magento\AdobeStockAsset\Model\Asset;
use Magento\AdobeStockAssetApi\Api\Data\AssetInterfaceFactory;
use Magento\AdobeStockAssetApi\Api\Data\AssetSearchResultsInterface;
use Magento\AdobeStockAssetApi\Api\Data\AssetSearchResultsInterfaceFactory;
use Magento\AdobeStockClientApi\Api\ClientInterface;
use Magento\AdobeStockImage\Model\GetImageList;
use Magento\Framework\Api\AttributeInterface;
use Magento\Framework\Api\Search\DocumentFactory;
use Magento\Framework\Api\Search\SearchResultInterface;
use Magento\Framework\Api\SearchCriteriaInterface;
use Magento\Framework\TestFramework\Unit\Helper\ObjectManager;
use PHPUnit\Framework\TestCase;

/**
 * Get image list test.
 */
class GetImageListTest extends TestCase
{
    /**
     * @var ObjectManager
     */
    private $objectManager;

    /**
     * @var GetImageList|\PHPUnit_Framework_MockObject_MockObject
     */
    private $getImageList;

    /**
     * @var ClientInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    private $clientMock;

    /**
     * @var AssetInterfaceFactory|\PHPUnit_Framework_MockObject_MockObject
     */
    private $assetFactoryMock;

    /**
     * @var AssetSearchResultsInterfaceFactory|\PHPUnit_Framework_MockObject_MockObject
     */
    private $assetSearchResultsInterfaceFactoryMock;

    /**
     * Prepare test objects.
     */
    public function setUp()
    {
        $this->objectManager = new ObjectManager($this);
        $this->clientMock = $this->getMockBuilder(ClientInterface::class)
            ->disableOriginalConstructor()
            ->setMethods(['search'])
            ->getMockForAbstractClass();
        /** @var AssetInterfaceFactory|\PHPUnit_Framework_MockObject_MockObject $assetFactoryMock */
        $this->assetFactoryMock = $this->getMockBuilder(AssetInterfaceFactory::class)
            ->disableOriginalConstructor()
            ->setMethods(['create'])
            ->getMockForAbstractClass();
        $this->assetSearchResultsInterfaceFactoryMock = $this->getMockBuilder(AssetSearchResultsInterfaceFactory::class)
            ->disableOriginalConstructor()
            ->setMethods(['create'])
            ->getMock();
        $this->getImageList = $this->objectManager->getObject(
            GetImageList::class,
            [
                'client' => $this->clientMock,
                'assetFactory' => $this->assetFactoryMock,
                'searchResultFactory' => $this->assetSearchResultsInterfaceFactoryMock
            ]
        );
    }

    /**
     * Test with founded items.
     */
    public function testExecuteWithItems()
    {
        $this->assetFactoryMock->expects($this->once())
            ->method('create')
            ->willReturn($this->objectManager->getObject(Asset::class));
        /** @var SearchCriteriaInterface $searchCriteriaMock */
        $searchCriteriaMock = $this->getMockBuilder(SearchCriteriaInterface::class)
            ->disableOriginalConstructor()
            ->getMock();
        $searchResultMock = $this->getMockBuilder(SearchResultInterface::class)
            ->disableOriginalConstructor()
            ->setMethods(['getItems', 'getTotalCount'])
            ->getMockForAbstractClass();
        $searchResultMock->expects($this->once())
            ->method('getItems')
            ->willReturn($this->getResultItems());
        $searchResultMock->expects($this->once())
            ->method('getTotalCount')
            ->willReturn(1);
        $this->clientMock->expects($this->once())
            ->method('search')
            ->with($searchCriteriaMock)
            ->willReturn($searchResultMock);
        $assetSearchResultsInterface = $this->getMockBuilder(AssetSearchResultsInterface::class)
            ->disableOriginalConstructor()
            ->getMockForAbstractClass();
        /** @var Asset $assetItem */
        $assetItem = $this->objectManager->getObject(Asset::class);
        $assetItem->setId(1);
        $assetItem->setUrl('http://example.com');
        $assetItem->setWidth(200);
        $assetItem->setHeight(100);
        $params = [
            'data' => [
                'items' => [$assetItem],
                'total_count' => 1
            ]
        ];
        $this->assetSearchResultsInterfaceFactoryMock->expects($this->once())
            ->method('create')
            ->with($params)
            ->willReturn($assetSearchResultsInterface);
        $imageListResult = $this->getImageList->execute($searchCriteriaMock);
        $this->assertInstanceOf(AssetSearchResultsInterface::class, $imageListResult);
    }

    /**
     * Test with didn't founded items.
     */
    public function testExecuteWithoutItems()
    {
        $this->assetFactoryMock->expects($this->any())
            ->method('create')
            ->willReturn($this->objectManager->getObject(Asset::class));
        /** @var SearchCriteriaInterface $searchCriteriaMock */
        $searchCriteriaMock = $this->getMockBuilder(SearchCriteriaInterface::class)
            ->disableOriginalConstructor()
            ->getMock();
        $searchResultMock = $this->getMockBuilder(SearchResultInterface::class)
            ->disableOriginalConstructor()
            ->setMethods(['getItems', 'getTotalCount'])
            ->getMockForAbstractClass();
        $searchResultMock->expects($this->once())
            ->method('getItems')
            ->willReturn([]);
        $searchResultMock->expects($this->once())
            ->method('getTotalCount')
            ->willReturn(0);
        $this->clientMock->expects($this->once())
            ->method('search')
            ->with($searchCriteriaMock)
            ->willReturn($searchResultMock);
        $assetSearchResultsInterface = $this->getMockBuilder(AssetSearchResultsInterface::class)
            ->disableOriginalConstructor()
            ->getMockForAbstractClass();
        $params = [
            'data' => [
                'items' => [],
                'total_count' => 0
            ]
        ];
        $this->assetSearchResultsInterfaceFactoryMock->expects($this->once())
            ->method('create')
            ->with($params)
            ->willReturn($assetSearchResultsInterface);
        $imageListResult = $this->getImageList->execute($searchCriteriaMock);
        $this->assertInstanceOf(AssetSearchResultsInterface::class, $imageListResult);
    }

    /**
     * Return result items.
     *
     * @return array
     */
    private function getResultItems(): array
    {
        $itemMock = $this->getMockBuilder(DocumentFactory::class)
            ->disableOriginalConstructor()
            ->setMethods(['getId', 'getCustomAttribute'])
            ->getMock();
        $itemMock->expects($this->once())
            ->method('getId')
            ->willReturn(1);
        $urlAttributeMock = $this->getMockBuilder(AttributeInterface::class)
            ->disableOriginalConstructor()
            ->setMethods(['getValue'])
            ->getMockForAbstractClass();
        $urlAttributeMock->expects($this->once())
            ->method('getValue')
            ->willReturn('http://example.com');
        $heightAttributeMock = $this->getMockBuilder(AttributeInterface::class)
            ->disableOriginalConstructor()
            ->setMethods(['getValue'])
            ->getMockForAbstractClass();
        $heightAttributeMock->expects($this->once())
            ->method('getValue')
            ->willReturn(100);
        $widthAttributeMock = $this->getMockBuilder(AttributeInterface::class)
            ->disableOriginalConstructor()
            ->setMethods(['getValue'])
            ->getMockForAbstractClass();
        $widthAttributeMock->expects($this->once())
            ->method('getValue')
            ->willReturn(200);
        $itemMock->expects($this->exactly(3))
            ->method('getCustomAttribute')
            ->withConsecutive(['url'], ['height'], ['width'])
            ->willReturnOnConsecutiveCalls($urlAttributeMock, $heightAttributeMock, $widthAttributeMock);

        return [$itemMock];
    }
}
