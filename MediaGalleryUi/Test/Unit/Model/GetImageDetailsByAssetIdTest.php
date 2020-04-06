<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Magento\MediaGalleryUi\Test\Unit\Model;

use Magento\Framework\Phrase;
use Magento\Framework\TestFramework\Unit\Helper\ObjectManager;
use Magento\MediaGallery\Model\Asset\Command\GetById;
use Magento\MediaGalleryApi\Api\Data\AssetInterface;
use Magento\MediaGalleryUi\Model\GetAssetGridDataById;
use Magento\MediaGalleryUi\Model\GetImageDetailsByAssetId;
use Magento\MediaGalleryUi\Ui\Component\Listing\Columns\SourceIconProvider;
use Magento\Store\Model\StoreManagerInterface;
use PHPUnit\Framework\MockObject\MockObject;
use Magento\Framework\UrlInterface;
use Magento\Store\Model\Store;
use PHPUnit\Framework\TestCase;

/**
 * Provides tests for getting image details by asset id.
 */
class GetImageDetailsByAssetIdTest extends TestCase
{
    /**
     * @var GetById|MockObject
     */
    private $getAssetByIdMock;

    /**
     * @var StoreManagerInterface|MockObject
     */
    private $storeManagerMock;

    /**
     * @var SourceIconProvider|MockObject
     */
    private $sourceIconProviderMock;

    /**
     * @var array
     */
    private $imageTypes;

    /**
     * @var GetImageDetailsByAssetId
     */
    private $getImageDetailsByAssetId;

    /**
     * @var Store|MockObject
     */
    protected $storeMock;

    /**
     * @var GetAssetGridDataById|MockObject
     */
    private $getAssetGridDataByIdMock;

    /**
     * Prepare test objects.
     */
    protected function setUp(): void
    {
        $this->getAssetByIdMock = $this->createMock(GetById::class);
        $this->storeManagerMock = $this->createMock(StoreManagerInterface::class);
        $this->sourceIconProviderMock = $this->createMock(SourceIconProvider::class);
        $this->getAssetGridDataByIdMock = $this->createMock(GetAssetGridDataById::class);
        $this->imageTypes = ['image' => 'Image'];

        $this->getImageDetailsByAssetId = (new ObjectManager($this))->getObject(
            GetImageDetailsByAssetId::class,
            [
                'getAssetById' => $this->getAssetByIdMock,
                'storeManager' => $this->storeManagerMock,
                'sourceIconProvider' => $this->sourceIconProviderMock,
                'getAssetGridDataById' => $this->getAssetGridDataByIdMock,
                'imageTypes' => $this->imageTypes
            ]
        );

        $this->storeMock = $this->createMock(Store::class);
    }

    /**
     * Test successful getting image details by asset id.
     *
     * @param int $assetId
     * @param AssetInterface $asset
     * @param array $assetGridData
     * @param array $expectedResult
     *
     * @dataProvider executeDataProvider
     */
    public function testExecute(
        int $assetId,
        AssetInterface $asset,
        array $assetGridData,
        array $expectedResult
    ): void {
        $this->getAssetByIdMock->expects($this->once())
            ->method('execute')
            ->with($assetId)
            ->willReturn($asset);

        $this->getAssetGridDataByIdMock->expects($this->once())
            ->method('execute')
            ->with($assetId)
            ->willReturn($assetGridData);

        $this->storeManagerMock->expects($this->any())
            ->method('getStore')
            ->willReturn($this->storeMock);

        $imageUrl = 'http://localhost/pub/media/';
        $this->storeMock->expects($this->any())
            ->method('getBaseUrl')
            ->with(UrlInterface::URL_TYPE_MEDIA)
            ->willReturn($imageUrl);

        $this->assertEquals($this->getImageDetailsByAssetId->execute($assetId), $expectedResult);
    }

    /**
     * Provides test case data.
     *
     * @return array
     */
    public function executeDataProvider(): array
    {
        return [
            [
                'assetId' => 42,
                'asset' => $this->configureAssetMock(),
                'assetGridData' => [
                    'keywords' => 'test, test keyword, nice test keyword',
                    'size' => 424242,
                    'content_type' => 'image/jpeg',
                    'source' => 'Adobe Stock',
                    'created_at' => '2020-04-04 12:00:00',
                    'updated_at' => '2020-04-04 12:00:00',
                    'width' => 6529,
                    'height' => 4355

                ],
                'expectedResult' => [
                    'image_url' => 'http://localhost/pub/media/catalog/category/test-image.jpeg',
                    'title' => 'Test asset title',
                    'id' => 42,
                    'details' => [
                        [
                            'title' => new Phrase('Type'),
                            'value' => 'Image',
                        ],
                        [
                            'title' => new Phrase('Created'),
                            'value' => '04/04/2020, 12:00 PM'
                        ],
                        [
                            'title' => new Phrase('Modified'),
                            'value' => '04/04/2020, 12:00 PM'
                        ],
                        [
                            'title' => new Phrase('Width'),
                            'value' => '6529px'
                        ],
                        [
                            'title' => new Phrase('Height'),
                            'value' => '4355px'
                        ],
                        [
                            'title' => new Phrase('Size'),
                            'value' => '424.242Kb'
                        ]
                    ],
                    'size' => 424242,
                    'tags' => ['test', ' test keyword', ' nice test keyword'],
                    'source' => null,
                    'content_type' => 'image/jpeg'
                ]
            ]
        ];
    }

    /**
     * Prepare an asset mock.
     *
     * @return MockObject
     */
    private function configureAssetMock(): MockObject
    {
        $assetMock = $this->createMock(AssetInterface::class);
        $assetMock->expects($this->any())
            ->method('getPath')
            ->willReturn('catalog/category/test-image.jpeg');

        $assetMock->expects($this->any())
            ->method('getTitle')
            ->willReturn('Test asset title');

        return $assetMock;
    }
}
