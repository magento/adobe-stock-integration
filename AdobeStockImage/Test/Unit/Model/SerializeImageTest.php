<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Magento\AdobeStockImage\Test\Unit\Model;

use Magento\AdobeStockAssetApi\Api\Data\AssetInterface;
use Magento\AdobeStockImage\Model\SerializeImage;
use Magento\Framework\Api\AttributeInterface;
use Magento\Framework\Api\Search\Document;
use Magento\Framework\Exception\SerializationException;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

/**
 * Test for SerializeImage Model
 */
class SerializeImageTest extends TestCase
{
    /**
     * @var AssetInterface|MockObject
     */
    private $documentMock;

    /**
     * @var SerializeImage
     */
    private $serializeImage;

    /**
     * @inheritdoc
     */
    protected function setUp(): void
    {
        $this->documentMock = $this->createMock(Document::class);
        $this->serializeImage = new SerializeImage();
    }

    /**
     * Testing execute method of serialize image class
     */
    public function testExecute(): void
    {
        $attributeCategoryMock = $this->createMock(AttributeInterface::class);
        $attributeCategoryMock->method('getAttributeCode')->willReturn('category');
        $attributeCategoryMock->method('getValue')->willReturn(123);
        $attribute240ThumbMock = $this->createMock(AttributeInterface::class);
        $attribute240ThumbMock->method('getAttributeCode')->willReturn('thumbnail_240_url');
        $attribute240ThumbMock->method('getValue')->willReturn("image-url");
        $this->documentMock->expects($this->once())
            ->method('getCustomAttributes')
            ->willReturn([$attribute240ThumbMock, $attributeCategoryMock]);
        $expectedResult = ['thumbnail_url' => 'image-url', 'category' => 123];
        $result = $this->serializeImage->execute($this->documentMock);
        $this->assertEquals($expectedResult, $result);
    }
}
