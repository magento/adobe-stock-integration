<?php
/**
 * Copyright 2024 Adobe
 * All Rights Reserved.
 */

declare(strict_types=1);

namespace Magento\AdobeStockImageAdminUi\Test\Unit\Model\Listing;

use Magento\AdobeStockImageAdminUi\Model\IsAdobeStockIntegrationEnabled;
use Magento\AdobeStockImageAdminUi\Model\Listing\DataProvider;
use Magento\AdobeStockImageApi\Api\GetImageListInterface;
use Magento\Framework\Api\AttributeInterface;
use Magento\Framework\Api\Search\Document;
use Magento\Framework\Api\Search\SearchCriteria;
use Magento\Framework\Api\Search\SearchCriteriaBuilder;
use Magento\Framework\Api\Search\SearchResultFactory;
use Magento\Framework\Api\Search\SearchResultInterface;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\TestFramework\Unit\Helper\ObjectManager;
use PHPUnit\Framework\Attributes\DataProvider as DataProviderAttribute;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

/**
 * Test data image provider.
 *
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class DataProviderTest extends TestCase
{
    /**
     * @var DataProvider
     */
    private $dataProvider;

    /**
     * @var GetImageListInterface|MockObject
     */
    private $getImageListMock;

    /**
     * @var SearchCriteriaBuilder|MockObject
     */
    private $searchCriteriaBuilder;

    /**
     * @var IsAdobeStockIntegrationEnabled|MockObject
     */
    private $isAdobeStockIntegrationEnabled;

    /**
     * @var SearchResultFactory|MockObject
     */
    private $searchResultFactory;

    /**
     * Prepare test objects.
     */
    protected function setUp(): void
    {
        $this->getImageListMock = $this->createMock(GetImageListInterface::class);
        $this->searchCriteriaBuilder = $this->createMock(SearchCriteriaBuilder::class);
        $this->isAdobeStockIntegrationEnabled = $this->createMock(IsAdobeStockIntegrationEnabled::class);
        $this->searchResultFactory = $this->createMock(SearchResultFactory::class);
        $this->dataProvider = (new ObjectManager($this))->getObject(
            DataProvider::class,
            [
                'name' => 'adobe_stock_images_listing_data_source',
                'primaryFieldName' => 'id',
                'requestFieldName' => 'id',
                'searchCriteriaBuilder' => $this->searchCriteriaBuilder,
                'getImageList' => $this->getImageListMock,
                'isAdobeStockIntegrationEnabled' => $this->isAdobeStockIntegrationEnabled,
                'searchResultFactory' => $this->searchResultFactory,
            ]
        );
    }

    /**
     * Test data in result.
     */
    public function testGetSearchResult(): void
    {
        $searchCriteria = $this->createMock(SearchCriteria::class);
        $searchCriteria->expects($this->once())
            ->method('setRequestName')
            ->with('adobe_stock_images_listing_data_source');

        $this->searchCriteriaBuilder->expects($this->once())
            ->method('create')
            ->willReturn($searchCriteria);

        /** @var SearchResultInterface|MockObject $searchResult */
        $searchResult = $this->createMock(SearchResultInterface::class);

        $this->getImageListMock->expects($this->once())
            ->method('execute')
            ->with($searchCriteria)
            ->willReturn($searchResult);

        $this->isAdobeStockIntegrationEnabled->expects($this->once())
            ->method('execute')
            ->willReturn(true);

        $this->assertEquals($searchResult, $this->dataProvider->getSearchResult());
    }

    /**
     * @param array $itemsData
     */
    #[DataProviderAttribute('itemsDataProvider')]
    public function testGetData(array $itemsData): void
    {
        $searchCriteria = $this->createMock(SearchCriteria::class);
        $searchCriteria->expects($this->once())
            ->method('setRequestName')
            ->with('adobe_stock_images_listing_data_source');

        $this->searchCriteriaBuilder->expects($this->once())
            ->method('create')
            ->willReturn($searchCriteria);

        $searchResult = $this->getSearchResult($itemsData);

        $this->getImageListMock->expects($this->once())
            ->method('execute')
            ->with($searchCriteria)
            ->willReturn($searchResult);

        $this->isAdobeStockIntegrationEnabled->expects($this->once())
            ->method('execute')
            ->willReturn(true);

        $data = [
            'items' => $itemsData,
            'totalRecords' => count($itemsData)
        ];

        $this->assertEquals($data, $this->dataProvider->getData());
    }

    /**
     * Verify LocalizedExceptions messages are returned in errorMessage data node
     */
    public function testGetDataException(): void
    {
        $searchCriteria = $this->createMock(SearchCriteria::class);
        $searchCriteria->expects($this->once())
            ->method('setRequestName')
            ->with('adobe_stock_images_listing_data_source');

        $this->searchCriteriaBuilder->expects($this->once())
            ->method('create')
            ->willReturn($searchCriteria);

        $this->getImageListMock->expects($this->once())
            ->method('execute')
            ->willThrowException(new LocalizedException(__('Localized error')));

        $this->isAdobeStockIntegrationEnabled->expects($this->once())
            ->method('execute')
            ->willReturn(true);

        $data = [
            'items' => [],
            'totalRecords' => 0,
            'errorMessage' => 'Localized error'
        ];

        $this->assertEquals($data, $this->dataProvider->getData());
    }

    /**
     * Test that empty result is returned when the integration is disabled, and the API is not called.
     */
    public function testGetSearchResultWhenAdobeStockIntegrationDisabled(): void
    {
        $searchCriteria = $this->createMock(SearchCriteria::class);
        $searchCriteria->expects($this->once())
            ->method('setRequestName')
            ->with('adobe_stock_images_listing_data_source');

        $this->searchCriteriaBuilder->expects($this->once())
            ->method('create')
            ->willReturn($searchCriteria);

        /** @var SearchResultInterface|MockObject $searchResult */
        $searchResult = $this->createMock(SearchResultInterface::class);

        $this->getImageListMock->expects($this->never())
            ->method('execute');

        $this->isAdobeStockIntegrationEnabled->expects($this->once())
            ->method('execute')
            ->willReturn(false);
        
        $this->searchResultFactory->expects($this->once())
            ->method('create')
            ->willReturn($searchResult);
        $searchResult->expects($this->once())
            ->method('setSearchCriteria')
            ->with($searchCriteria);
        $searchResult->expects($this->once())
            ->method('setItems')
            ->with([]);
        $searchResult->expects($this->once())
            ->method('setTotalCount')
            ->with(0);

        $this->assertEquals($searchResult, $this->dataProvider->getSearchResult());
    }

    /**
     * @param array $itemsData
     * @return MockObject
     */
    private function getSearchResult(array $itemsData): MockObject
    {
        $items = [];

        foreach ($itemsData as $itemData) {
            $item = $this->createMock(Document::class);
            $attributes = [];
            foreach ($itemData as $key => $value) {
                $attribute = $this->createMock(AttributeInterface::class);
                $attribute->expects($this->once())
                    ->method('getAttributeCode')
                    ->willReturn($key);
                $attribute->expects($this->once())
                    ->method('getValue')
                    ->willReturn($value);
                $attributes[] = $attribute;
            }
            $item->expects($this->once())
                ->method('getCustomAttributes')
                ->willReturn($attributes);
            $items[] = $item;
        }
        /** @var SearchResultInterface|MockObject $searchResult */
        $searchResult = $this->createMock(SearchResultInterface::class);
        $searchResult->expects($this->once())
            ->method('getItems')
            ->willReturn($items);
        $searchResult->expects($this->once())
            ->method('getTotalCount')
            ->willReturn(count($items));

        return $searchResult;
    }

    /**
     * @return array
     */
    public static function itemsDataProvider(): array
    {
        $itemsData = [
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

        return [
            [$itemsData]
        ];
    }
}
