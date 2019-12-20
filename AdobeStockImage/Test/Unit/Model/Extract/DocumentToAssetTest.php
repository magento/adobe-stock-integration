<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Magento\AdobeStockImage\Test\Unit\Model\Extract;

use Magento\AdobeStockAssetApi\Api\Data\AssetInterface;
use Magento\AdobeStockAssetApi\Api\Data\AssetInterfaceFactory;
use Magento\AdobeStockAssetApi\Api\Data\CategoryInterface;
use Magento\AdobeStockAssetApi\Api\Data\CategoryInterfaceFactory;
use Magento\AdobeStockAssetApi\Api\Data\CreatorInterface;
use Magento\AdobeStockAssetApi\Api\Data\CreatorInterfaceFactory;
use Magento\AdobeStockImage\Model\Extract\AdobeStockAsset;
use Magento\Framework\Api\AttributeInterface;
use Magento\Framework\Api\Search\Document;
use Magento\Framework\TestFramework\Unit\Helper\ObjectManager;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

/**
 * Test converting a Document from the search result to Adobe Stock asset
 */
class DocumentToAssetTest extends TestCase
{
    /**
     * @var AdobeStockAsset
     */
    private $documentToAsset;

    /**
     * @var CategoryInterfaceFactory|MockObject
     */
    private $creatorFactory;

    /**
     * @var CategoryInterfaceFactory|MockObject
     */
    private $categoryFactory;

    /**
     * @var AssetInterfaceFactory|MockObject
     */
    private $assetFactory;

    /**
     * Prepare test objects.
     */
    protected function setUp(): void
    {
        $this->assetFactory = $this->createMock(AssetInterfaceFactory::class);
        $this->creatorFactory = $this->createMock(CreatorInterfaceFactory::class);
        $this->categoryFactory = $this->createMock(CategoryInterfaceFactory::class);

        $this->documentToAsset = (new ObjectManager($this))->getObject(
            AdobeStockAsset::class,
            [
                'assetFactory' => $this->assetFactory,
                'creatorFactory' => $this->creatorFactory,
                'categoryFactory' => $this->categoryFactory,
            ]
        );
    }

    /**
     * @dataProvider documentProvider
     * @param array $assetData
     * @param array $cateogryData
     * @param array $creatorData
     * @param Document $document
     * @param array $additionalData
     */
    public function testConvert(
        array $assetData,
        array $cateogryData,
        array $creatorData,
        Document $document,
        array $additionalData
    ): void {
        $asset = $this->createMock(AssetInterface::class);
        $creator = $this->createMock(CreatorInterface::class);
        $category = $this->createMock(CategoryInterface::class);

        $assetData['category'] = $category;
        $assetData['creator'] = $creator;

        $this->assetFactory->expects($this->once())
            ->method('create')
            ->with(['data' => $assetData])
            ->willReturn($asset);
        $this->categoryFactory->expects($this->once())
            ->method('create')
            ->with(['data' => $cateogryData])
            ->willReturn($category);
        $this->creatorFactory->expects($this->once())
            ->method('create')
            ->with(['data' => $creatorData])
            ->willReturn($creator);

        $this->assertInstanceOf(AssetInterface::class, $this->documentToAsset->convert($document, $additionalData));
    }

    /**
     * @return array
     */
    public function documentProvider(): array
    {
        return [
            'case1' => [
                'assetData' => [
                    'id' => 1,
                    'is_licensed' => 1,
                    'media_gallery_id' => 5
                ],
                'categoryData' => [
                    'id' => 2,
                    'name' => 'The Category'
                ],
                'creatorData' => [
                    'name' => 'Creator',
                    'id' => 3,
                ],
                'document' => $this->getDocument(
                    [
                        'id' => 1,
                        'category' => [
                            'id' => 2,
                            'name' => 'The Category'
                        ],
                        'creator_name' => 'Creator',
                        'creator_id' => 3,
                        'is_licensed' => 1
                    ]
                ),
                [
                    'media_gallery_id' => 5
                ]
            ]
        ];
    }

    /**
     * @param array $attributes
     * @return MockObject
     */
    private function getDocument(array $attributes): MockObject
    {
        $document = $this->createMock(Document::class);

        $attributeMocks = [];

        foreach ($attributes as $key => $value) {
            $attribute = $this->createMock(AttributeInterface::class);
            $attribute->expects($this->any())
                ->method('getAttributeCode')
                ->willReturn($key);
            $attribute->expects($this->any())
                ->method('getValue')
                ->willReturn($value);
            $attributeMocks[] = $attribute;
        }

        $document->expects($this->once())
            ->method('getCustomAttributes')
            ->willReturn($attributeMocks);

        return $document;
    }
}
