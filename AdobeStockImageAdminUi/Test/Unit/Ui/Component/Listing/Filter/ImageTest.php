<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Magento\AdobeStockImageAdminUi\Test\Unit\Ui\Component\Listing\Columns;

use Magento\AdobeStockImageAdminUi\Ui\Component\Listing\Columns\Image;
use Magento\Framework\TestFramework\Unit\Helper\ObjectManager;
use PHPUnit\Framework\TestCase;

/**
 * Test image UI component.
 */
class ImageTest extends TestCase
{
    /**
     * @var ObjectManager
     */
    private $objectManager;

    /**
     * @var Image
     */
    private $image;

    /**
     * Prepare test objects.
     */
    public function setUp()
    {
        $this->objectManager = new ObjectManager($this);
        $this->image = $this->objectManager->getObject(Image::class);
    }

    /**
     * Prepare data source with items.
     *
     * @param array $dataSourceItems
     * @return void
     * @dataProvider dataSourceItemsDataProvider
     */
    public function testPrepareDataSourceWithItems(array $dataSourceItems): void
    {
        $dataSource = [
            'data' => [
                'items' => $dataSourceItems,
                'totalRecords' => 3,
            ]
        ];
        $this->image->setData('name', 'url');
        $preparedDataSource = $this->image->prepareDataSource($dataSource);
        foreach ($preparedDataSource['data']['items'] as $item) {
            $this->assertTrue(isset($item['url_src']));
        }
    }

    /**
     * Prepare data source without items.
     *
     * @return void
     */
    public function testPrepareDataSourceWithoutItems(): void
    {
        $dataSource = [
            'data' => [
                'items' => [],
                'totalRecords' => 0,
            ]
        ];
        $this->image->setData('name', 'url');
        $preparedDataSource = $this->image->prepareDataSource($dataSource);
        $this->assertEquals($dataSource, $preparedDataSource);
    }

    /**
     * Prepare data source with not found field on items.
     *
     * @param array $dataSourceItems
     * @return void
     * @dataProvider dataSourceItemsDataProvider
     */
    public function testPrepareDataSourceWithNotFoundField(array $dataSourceItems): void
    {
        $dataSource = [
            'data' => [
                'items' => $dataSourceItems,
                'totalRecords' => 3,
            ]
        ];
        $this->image->setData('name', 'some_random_field');
        $preparedDataSource = $this->image->prepareDataSource($dataSource);
        $this->assertEquals($dataSource, $preparedDataSource);
    }

    /**
     * Data source items.
     *
     * @return array
     */
    public function dataSourceItemsDataProvider(): array
    {
        return [
            [
                [
                    0 => [
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
                    1 => [
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
                    2 => [
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
                ]
            ]
        ];
    }
}
