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
use Magento\AdobeStockAssetApi\Api\Data\CategoryInterfaceFactory;
use Magento\AdobeStockAssetApi\Api\Data\CreatorInterfaceFactory;
//use Magento\AdobeStockAssetApi\Api\Data\KeywordInterfaceFactory;
use Magento\Framework\Api\Search\DocumentInterface;
use PHPUnit\Framework\TestCase;
use PHPUnit_Framework_MockObject_MockObject as MockObject;

/**
 * Document to asset test.
 */
class DocumentToAssetTest extends TestCase
{
    /**
     * @var $documentToAsset
     */
    private $documentToAsset;

    /**
     * @var DocumentInterface|\PHPUnit_Framework_MockObject_MockObject $document
     */
    private $document;

    /**
     * @var MockObject $creatorFactory
     */
    private $creatorFactory;

    /**
     * @var MockObject $categoryFactory
     */
    private $categoryFactory;

    /**
     * @var MockObject $keyworFactory
     */
    private $keywordFactory;

    /**
     * @var MockObject $assetFactory ;
     */
    private $assetFactory;

    /**
     * Prepare test objects.
     */
    public function setUp(): void
    {
        $this->document = $this->getMockBuilder(\Magento\Framework\Api\Search\DocumentInterface::class)
            ->getMockForAbstractClass();
        $this->document->setId(280812991);
        $this->assetFactory = $this->getMockBuilder(AssetInterfaceFactory::class)
            ->disableOriginalConstructor()
            ->getMock();
        $this->creatorFactory = $this->getMockBuilder(CreatorInterfaceFactory::class)
            ->disableOriginalConstructor()
            ->getMock();
        $this->categoryFactory = $this->getMockBuilder(CategoryInterfaceFactory::class)
            ->disableOriginalConstructor()
            ->getMock();
        $this->keywordFactory = $this->getMockBuilder(KeywordInterfaceFactory::class)
            ->disableOriginalConstructor()
            ->getMock();
        $attributes = [
            'factory' => $this->assetFactory,
            'fields' => ['thumbnail_240_urll' => 'thumbnail_url', 'thumbnail_500_url' => 'preview_url'],
            'children' => [
                'creator' => [
                    'factory' => $this->creatorFactory,
                    'fields' => ['creator_id' => 'id', 'creator_name' => 'name']
                ],
                'category' => [
                    'factory' => $this->categoryFactory,
                    'fields' => ['category_id' => 'id', 'category_name' => 'name'],
                ],
            ]
        ];
        $this->document->setCustomAttributes($attributes);
        $this->documentToAsset = new DocumentToAsset(($attributes));
    }

    /**
     * Test convert.
     */
    public function testConvert(): void
    {
        $this->assetFactory->expects($this->once())
            ->method('create')
            ->willReturn(
                $this->getMockBuilder(\Magento\AdobeStockAsset\Model\Asset::class)
                    ->disableOriginalConstructor()
                    ->getMock()
            );
        $this->categoryFactory->expects($this->once())
            ->method('create')
            ->willReturn(
                $this->getMockBuilder(\Magento\AdobeStockAsset\Model\Category::class)
                    ->disableOriginalConstructor()
                    ->getMock()
            );
        $this->creatorFactory->expects($this->once())
            ->method('create')
            ->willReturn(
                $this->getMockBuilder(\Magento\AdobeStockAsset\Model\Creator::class)
                    ->disableOriginalConstructor()
                    ->getMock()
            );
        $this->document->expects($this->once())
            ->method('getCustomAttributes')
            ->willReturn(
                [
                    'id_field_name' => new \Magento\Framework\DataObject(
                        ['_data' => ['attribute_code' => "id_field_name", 'value' => 'id']]
                    )
                ]
            );
        $dataObjectMock = $this->getMockBuilder(\Magento\Framework\DataObject::class)
            ->disableOriginalConstructor()
            ->setMethods(['getAttributeCode'])
            ->getMock();
        $dataObjectMock->expects($this->any())->method('getAttributeCode')->willReturn('id_field_name');
        $this->assertInstanceOf(AssetInterface::class, $this->documentToAsset->convert($this->document));
    }
}
