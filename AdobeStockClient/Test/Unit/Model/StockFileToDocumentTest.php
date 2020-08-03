<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Magento\AdobeStockClient\Test\Unit\Model;

use AdobeStock\Api\Models\StockFile;
use Magento\AdobeStockClient\Model\StockFileToDocument;
use Magento\Framework\Api\AttributeValue;
use Magento\Framework\Api\AttributeValueFactory;
use Magento\Framework\Api\Search\Document;
use Magento\Framework\Api\Search\DocumentFactory;
use Magento\Framework\TestFramework\Unit\Helper\ObjectManager;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Psr\Log\LoggerInterface;

class StockFileToDocumentTest extends TestCase
{
    /**
     * @var ObjectManager
     */
    private $objectManager;

    /**
     * @var StockFileToDocument|MockObject
     */
    private $stockFileToDocument;

    /**
     * @var DocumentFactory|MockObject
     */
    private $documentFactory;

    /**
     * @var AttributeValueFactory|MockObject
     */
    private $attributeValueFactory;

    /**
     * @inheritdoc
     */
    protected function setUp(): void
    {
        $this->objectManager = new ObjectManager($this);

        $this->documentFactory = $this->createMock(DocumentFactory::class);
        $this->attributeValueFactory = $this->createMock(AttributeValueFactory::class);
        $logger = $this->createMock(LoggerInterface::class);

        $this->stockFileToDocument = $this->objectManager->getObject(
            StockFileToDocument::class,
            [
                'documentFactory' => $this->documentFactory,
                'attributeValueFactory' => $this->attributeValueFactory,
                'logger' => $logger,
            ]
        );
    }

    /**
     * @param StockFile $stockFile
     * @param array $attributesData
     *
     * @dataProvider convertDataProvider
     */
    public function testConvert(StockFile $stockFile, array $attributesData): void
    {
        $item = $this->createMock(Document::class);

        $i = 0;
        foreach ($attributesData as $attributeKey => $attributeValue) {
            $attribute = $this->createMock(AttributeValue::class);

            $this->attributeValueFactory->expects($this->at($i))
                ->method('create')
                ->willReturn($attribute);

            $attribute->expects($this->once())
                ->method('setValue')
                ->with($attributeValue);

            $attribute->expects($this->once())
                ->method('setAttributeCode')
                ->with($attributeKey);
            $i++;
        }

        $this->documentFactory->expects($this->once())
            ->method('create')
            ->willReturn($item);
        $item->expects($this->once())
            ->method('setId')
            ->with($stockFile->getId())
            ->willReturn($item);
        $item->expects($this->once())
            ->method('setCustomAttributes')
            ->willReturn($item);

        $this->stockFileToDocument->convert($stockFile);
    }

    /**
     * @return array
     */
    public function convertDataProvider(): array
    {
        $stockFile = new StockFile([]);

        $stockFileId = 5;
        $categoryId = 1;
        $categoryName = 'test_category';
        $categoryTitle = 'test_title';
        $creatorName = 'test_creator';
        $keyWords = ['test', 'test2', 'test3'];
        $countryName = 'USA';
        $description = 'Test description';

        $stockFile->setId($stockFileId);
        $stockFile->setCategory($categoryId, $categoryName);
        $stockFile->setTitle($categoryTitle);
        $stockFile->setCreatorName($creatorName);
        $stockFile->setKeywords($keyWords);
        $stockFile->setCountryName($countryName);
        $stockFile->setDescription($description);

        $attributesData = [
            'id_field_name' => 'id',
            'id' => $stockFileId,
            'title' => $categoryTitle,
            'creator_name' => $creatorName,
            'country_name' => $countryName,
            'category' => [
                'id' => $categoryId,
                'name' => $categoryName,
                'link' => null,
            ],
            'keywords' => $keyWords,
            'description' => $description,
            'category_id' => $categoryId,
            'category_name' => $categoryName,
        ];

        return [
            [$stockFile, $attributesData]
        ];
    }
}
