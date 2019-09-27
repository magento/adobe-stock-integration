<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Magento\AdobeStockClient\Test\Unit\Model;

use AdobeStock\Api\Models\StockFile;
use Magento\AdobeStockClient\Model\StockFileToDocument;
use Magento\Framework\Api\AttributeValueFactory;
use Magento\Framework\Api\Search\DocumentFactory;
use Magento\Framework\TestFramework\Unit\Helper\ObjectManager;
use PHPUnit\Framework\TestCase;
use Psr\Log\LoggerInterface;

class StockFileToDocumentTest extends TestCase
{
    /**
     * @var ObjectManager
     */
    private $objectManager;

    /**
     * @var StockFileToDocument|\PHPUnit_Framework_MockObject_MockObject
     */
    private $stockFileToDocument;

    /**
     * @var DocumentFactory
     */
    private $documentFactory;

    /**
     * @var AttributeValueFactory
     */
    private $attributeValueFactory;

    /**
     * @inheritdoc
     */
    public function setUp(): void
    {
        $this->objectManager = new ObjectManager($this);

        $this->documentFactory = $this->getMockBuilder(DocumentFactory::class)
            ->disableOriginalConstructor()
            ->getMock();
        $this->attributeValueFactory = $this->getMockBuilder(AttributeValueFactory::class)
            ->disableOriginalConstructor()
            ->getMock();
        $logger = $this->getMockBuilder(LoggerInterface::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->stockFileToDocument = $this->objectManager->getObject(
            StockFileToDocument::class,
            [
                'documentFactory' => $this->documentFactory,
                'attributeValueFactory' => $this->attributeValueFactory,
                'logger' => $logger,
            ]
        );
    }

    public function testConvert()
    {
        /** @var StockFile $stockFile */
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

        $attribute = $this->getMockBuilder(\Magento\Framework\Api\AttributeValue::class)
            ->setMethods(['setAttributeCode', 'setValue'])
            ->disableOriginalConstructor()
            ->getMock();
        $item = $this->getMockBuilder(\Magento\Framework\Api\Search\Document::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->attributeValueFactory->expects($this->exactly(10))
            ->method('create')
            ->willReturn($attribute);
        $attribute->expects($this->exactly(10))
            ->method('setAttributeCode')
            ->withConsecutive(
                [$this->identicalTo('id_field_name')],
                [$this->identicalTo('id')],
                [$this->identicalTo('title')],
                [$this->identicalTo('creator_name')],
                [$this->identicalTo('country_name')],
                [$this->identicalTo('category')],
                [$this->identicalTo('keywords')],
                [$this->identicalTo('description')],
                [$this->identicalTo('category_id')],
                [$this->identicalTo('category_name')]
            );
        $attribute->expects($this->exactly(10))
            ->method('setValue')
            ->withConsecutive(
                [$this->identicalTo('id')],
                [$this->identicalTo($stockFileId)],
                [$this->identicalTo($categoryTitle)],
                [$this->identicalTo($creatorName)],
                [$this->identicalTo($countryName)],
                [
                    $this->identicalTo([
                        'id' => $categoryId,
                        'name' => $categoryName,
                        'link' => null,
                    ])
                ],
                [$this->identicalTo($keyWords)],
                [$this->identicalTo($description)],
                [$this->identicalTo($categoryId)],
                [$this->identicalTo($categoryName)]
            );
        $this->documentFactory->expects($this->once())
            ->method('create')
            ->willReturn($item);
        $item->expects($this->once())
            ->method('setId')
            ->with($stockFileId)
            ->willReturn($item);
        $item->expects($this->once())
            ->method('setCustomAttributes')
            ->willReturn($item);

        $this->stockFileToDocument->convert($stockFile);
    }
}
