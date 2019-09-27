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
            ->setMethods([])
            ->disableOriginalConstructor()
            ->getMock();
        $this->attributeValueFactory = $this->getMockBuilder(AttributeValueFactory::class)
            ->setMethods([])
            ->disableOriginalConstructor()
            ->getMock();
        $logger = $this->getMockBuilder(LoggerInterface::class)
            ->setMethods([])
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
        $stockFile->setId($stockFileId);
        $stockFile->setCategory(1, 'test_category');

        $idFieldNameAttribute = $this->getMockBuilder(\Magento\Framework\Api\AttributeValue::class)
            ->setMethods(['setAttributeCode', 'setValue'])
            ->disableOriginalConstructor()
            ->getMock();
        $item = $this->getMockBuilder(\Magento\Framework\Api\Search\Document::class)
            ->setMethods(['setId', 'setCustomAttributes'])
            ->disableOriginalConstructor()
            ->getMock();

        $this->attributeValueFactory->expects($this->any())
            ->method('create')
            ->willReturn($idFieldNameAttribute);
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
