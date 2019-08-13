<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

declare(strict_types=1);

namespace Magento\AdobeStockImageAdminUi\Test\Unit\Model\Listing;

use Magento\AdobeStockAssetApi\Api\Data\AssetSearchResultsInterface;
use Magento\AdobeStockImageAdminUi\Model\Listing\DataProvider;
use Magento\AdobeStockImageApi\Api\GetImageListInterface;
use Magento\Framework\Api\Search\SearchCriteria;
use Magento\Framework\Api\Search\SearchCriteriaBuilder;
use Magento\Framework\Api\Search\SearchResultInterface;
use Magento\Framework\TestFramework\Unit\Helper\ObjectManager;
use Magento\Ui\DataProvider\SearchResultFactory;
use PHPUnit\Framework\TestCase;

/**
 * Test data image provider.
 */
class DataProviderTest extends TestCase
{
    /**
     * @var ObjectManager
     */
    private $objectManager;

    /**
     * @var DataProvider
     */
    private $dataProvider;

    /**
     * @var SearchResultFactory|\PHPUnit_Framework_MockObject_MockObject
     */
    private $searchResultFactory;

    /**
     * @var GetImageListInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    private $getImageListMock;

    /**
     * @var SearchCriteriaBuilder|\PHPUnit_Framework_MockObject_MockObject
     */
    private $searchCriteriaBuilder;

    /**
     * Prepare test objects.
     */
    public function setUp(): void
    {
        $this->objectManager = new ObjectManager($this);
        $this->searchResultFactory = $this->getMockBuilder(SearchResultFactory::class)
            ->setMethods(['create'])
            ->disableOriginalConstructor()
            ->getMock();
        $this->getImageListMock = $this->getMockBuilder(GetImageListInterface::class)
            ->setMethods(['execute'])
            ->disableOriginalConstructor()
            ->getMockForAbstractClass();
        $this->searchCriteriaBuilder = $this->getMockBuilder(SearchCriteriaBuilder::class)
            ->disableOriginalConstructor()
            ->getMock();
        $this->dataProvider = $this->objectManager->getObject(
            DataProvider::class,
            [
                'name' => 'adobe_stock_images_listing_data_source',
                'primaryFieldName' => 'id',
                'requestFieldName' => 'id',
                'searchCriteriaBuilder' => $this->searchCriteriaBuilder,
                'searchResultFactory' => $this->searchResultFactory,
                'getImageList' => $this->getImageListMock,
            ]
        );
    }

    /**
     * Test data in result.
     */
    public function testGetTestSearchResult(): void
    {
        $items = [
            [
                'id_field_name' => 'id',
                'id' => 273563073,
                'path' => '',
                'url' => 'https://test.com/image1.jpg',
                'height' => 3664,
                'width' => 14136,
                'media_type_id' => 0,
                'keywords' => [],
                'premium_level_id' => 0,
                'adobe_id' => 0,
                'stock_id' => 0,
                'licensed' => 0,
                'title' => '',
                'preview_url' => '',
                'preview_width' => 0,
                'preview_height' => 0,
                'country_name' => '',
                'details_url' => '',
                'vector_type' => '',
                'content_type' => '',
                'creation_date' => '',
                'created_at' => '',
                'updated_at' => ''
            ],
            [
                'id_field_name' => 'id',
                'id' => 272239824,
                'path' => '',
                'url' => 'https://test.com/image2.jpg',
                'height' => 7264,
                'width' => 13111,
                'media_type_id' => 0,
                'keywords' => [],
                'premium_level_id' => 0,
                'adobe_id' => 0,
                'stock_id' => 0,
                'licensed' => 0,
                'title' => '',
                'preview_url' => '',
                'preview_width' => 0,
                'preview_height' => 0,
                'country_name' => '',
                'details_url' => '',
                'vector_type' => '',
                'content_type' => '',
                'creation_date' => '',
                'created_at' => '',
                'updated_at' => ''
            ],
            [
                'id_field_name' => 'id',
                'id' => 272492011,
                'path' => '',
                'url' => 'https://test.com/image3.jpg',
                'height' => 4000,
                'width' => 6000,
                'media_type_id' => 0,
                'keywords' => [],
                'premium_level_id' => 0,
                'adobe_id' => 0,
                'stock_id' => 0,
                'licensed' => 0,
                'title' => '',
                'preview_url' => '',
                'preview_width' => 0,
                'preview_height' => 0,
                'country_name' => '',
                'details_url' => '',
                'vector_type' => '',
                'content_type' => '',
                'creation_date' => '',
                'created_at' => '',
                'updated_at' => ''
            ],
        ];
        $totalCount = 3;

        $searchCriteria = $this->getMockBuilder(SearchCriteria::class)
            ->disableOriginalConstructor()
            ->getMock();
        $searchCriteria->expects($this->once())
            ->method('setRequestName')
            ->with('adobe_stock_images_listing_data_source');
        $assetSearchResult = $this->getMockBuilder(AssetSearchResultsInterface::class)
            ->setMethods(['getItems', 'getTotalCount'])
            ->disableOriginalConstructor()
            ->getMockForAbstractClass();
        $assetSearchResult->expects($this->once())
            ->method('getItems')
            ->willReturn($items);
        $assetSearchResult->expects($this->once())
            ->method('getTotalCount')
            ->willReturn($totalCount);
        $this->searchCriteriaBuilder->expects($this->once())
            ->method('create')
            ->willReturn($searchCriteria);
        $this->getImageListMock->expects($this->once())
            ->method('execute')
            ->with($searchCriteria)
            ->willReturn($assetSearchResult);
        $result = $this->getMockBuilder(SearchResultInterface::class)
            ->disableOriginalConstructor()
            ->getMockForAbstractClass();
        $this->searchResultFactory->expects($this->once())
            ->method('create')
            ->with($items, $totalCount, $searchCriteria, 'id')
            ->willReturn($result);
        $assetSearchResult = $this->dataProvider->getSearchResult();
        $this->assertInstanceOf(SearchResultInterface::class, $assetSearchResult);
    }
}
