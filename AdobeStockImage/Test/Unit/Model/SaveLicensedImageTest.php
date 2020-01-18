<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

declare(strict_types=1);

namespace Magento\AdobeStockImage\Test\Unit\Model;

use Magento\AdobeStockAssetApi\Api\Data\AssetInterface;
use Magento\AdobeStockAssetApi\Api\GetAssetByIdInterface;
use Magento\AdobeStockClientApi\Api\ClientInterface;
use Magento\AdobeStockImage\Model\SaveLicensedImage;
use Magento\AdobeStockImageApi\Api\SaveImageInterface;
use Magento\Framework\Api\Search\Document;
use Magento\Framework\DataObject;
use Magento\Framework\TestFramework\Unit\Helper\ObjectManager;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

/**
 * Test saving the Adobe Stock Image that was licensed on Adobe Stock previously
 */
class SaveLicensedImageTest extends TestCase
{
    /**
     * @var SaveLicensedImage
     */
    private $sut;

    /**
     * @var ClientInterface|MockObject
     */
    private $clientInterfaceMock;

    /**
     * @var SaveImageInterface|MockObject
     */
    private $saveImageMock;

    /**
     * @var GetAssetByIdInterface|MockObject
     */
    private $getAssetByIdMock;

    /**
     * @var AssetInterface|MockObject
     */
    private $documentMock;

    /**
     * @inheritdoc
     */
    protected function setUp(): void
    {
        $this->clientInterfaceMock = $this->createMock(ClientInterface::class);
        $this->saveImageMock = $this->createMock(SaveImageInterface::class);
        $this->getAssetByIdMock = $this->createMock(GetAssetByIdInterface::class);
        $this->documentMock = $this->createMock(Document::class);

        $this->sut = (new ObjectManager($this))->getObject(
            SaveLicensedImage::class,
            [
                'client' => $this->clientInterfaceMock,
                'saveImage' => $this->saveImageMock,
                'getAssetById' => $this->getAssetByIdMock
            ]
        );
    }

    /**
     * Test
     */
    public function testExecute(): void
    {
        $mediaId = 283415387;
        $destinationPath = 'destination_path';
        $imageUrl = 'http://image_url.jpg';

        $this->documentMock->expects($this->once())
            ->method('getCustomAttribute')
            ->willReturn(new DataObject(['value' => '']));
        $this->getAssetByIdMock->expects($this->once())
            ->method('execute')
            ->with($mediaId)
            ->willReturn($this->documentMock);
        $this->clientInterfaceMock->expects($this->once())
            ->method('getImageDownloadUrl')
            ->willReturn($imageUrl);
        $this->saveImageMock->expects($this->once())
            ->method('execute')
            ->with($this->documentMock, $imageUrl, $destinationPath);

        $this->sut->execute($mediaId, $destinationPath);
    }
}
