<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Magento\AdobeStockAsset\Test\Unit\Model;

use Magento\AdobeStockAsset\Model\DocumentToAsset;
use Magento\AdobeStockAssetApi\Api\Data\AssetInterface;
use Magento\AdobeStockAssetApi\Api\Data\AssetInterfaceFactory;
use Magento\AdobeStockAssetApi\Api\Data\CategoryInterface;
use Magento\AdobeStockAssetApi\Api\Data\CategoryInterfaceFactory;
use Magento\AdobeStockAssetApi\Api\Data\CreatorInterface;
use Magento\AdobeStockAssetApi\Api\Data\CreatorInterfaceFactory;
use Magento\Framework\Api\AttributeInterface;
use Magento\Framework\Api\Search\DocumentInterface;
use Magento\Framework\TestFramework\Unit\Helper\ObjectManager;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

/**
 * Document to asset test.
 */
class DocumentToAssetTest extends TestCase
{
    /**
     * @var DocumentToAsset
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
    public function setUp(): void
    {
        $this->assetFactory = $this->createMock(AssetInterfaceFactory::class);
        $this->creatorFactory = $this->createMock(CreatorInterfaceFactory::class);
        $this->categoryFactory = $this->createMock(CategoryInterfaceFactory::class);

        $this->documentToAsset = (new ObjectManager($this))->getObject(
            DocumentToAsset::class,
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
     * @param DocumentInterface $document
     * @param array $additionalData
     */
    public function testConvert(
        array $assetData,
        array $cateogryData,
        array $creatorData,
        DocumentInterface $document,
        array $additionalData
    ): void
    {
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

    private function getDocument(array $attributes)
    {
        $document = $this->createMock(DocumentInterface::class);

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
