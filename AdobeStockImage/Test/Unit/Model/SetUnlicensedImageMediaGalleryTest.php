<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

declare(strict_types=1);

namespace Magento\AdobeStockImage\Test\Unit\Model;

use Magento\AdobeStockAssetApi\Api\Data\AssetInterface;
use Magento\AdobeStockImage\Model\SetUnlicensedImageMediaGallery;
use Magento\Framework\App\ResourceConnection;
use Magento\Framework\DB\Adapter\AdapterInterface;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Phrase;
use Magento\Framework\TestFramework\Unit\Helper\ObjectManager;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Psr\Log\LoggerInterface;

/**
 * Verify setting unlicensed lable for media gallery grid
 */
class SetUnlicensedImageMediaGalleryTest extends TestCase
{
    /**
     * @var ResourceConnection|MockObject
     */
    private $resourceConnectionMock;

    /**
     * @var LoggerInterface|MockObject
     */
    private $loggerMock;

    /**
     * @var SetUnlicensedImageMediaGallery
     */
    private $setUnlicensedImagesMediaGallery;

    /**
     * @var ObjectManager
     */
    private $objectManager;

    /**
     * @var AssetInterface|MockObject
     */
    private $assetMock;

    /**
     * @var AdapterInterface|MockObject
     */
    private $adapterMock;

    /**
     * @inheritdoc
     */
    protected function setUp(): void
    {
        $this->objectManager = new ObjectManager($this);
        $this->loggerMock = $this->createMock(LoggerInterface::class);
        $this->resourceConnectionMock = $this->createMock(ResourceConnection::class);
        $this->assetMock = $this->createMock(AssetInterface::class);
        $this->adapterMock = $this->createMock(AdapterInterface::class);

        $this->setUnlicensedImagesMediaGallery = $this->objectManager->getObject(
            SetUnlicensedImageMediaGallery::class,
            [
                'resource' => $this->resourceConnectionMock,
                'logger' => $this->loggerMock
            ]
        );
    }

    /**
     * Verfy set licensed label
     */
    public function testExecute(): void
    {
        $this->resourceConnectionMock->expects($this->once())
            ->method('getConnection')
            ->willReturn($this->adapterMock);
        $this->resourceConnectionMock->expects($this->once())
            ->method('getTableName')
            ->with('media_gallery_asset_grid')
            ->willReturn('media_gallery_asset_grid');
        $this->assetMock->expects($this->once())
            ->method('getMediaGalleryId')
            ->willReturn(1);
        $this->assetMock->expects($this->once())
            ->method('getIsLicensed')
            ->willReturn(0);
        $this->adapterMock->expects($this->once())
            ->method('insertOnDuplicate')
            ->with(
                'media_gallery_asset_grid',
                [
                    'id' => 1,
                    'licensed' => 0
                ]
            )
            ->willReturnSelf();

        $this->setUnlicensedImagesMediaGallery->execute($this->assetMock);
    }

    /**
     * Verify execute method with Exception
     *
     * @return void
     */
    public function testExecuteWithException(): void
    {
        $this->resourceConnectionMock->expects($this->once())
            ->method('getConnection')
            ->willReturn($this->adapterMock);
        $this->adapterMock->expects($this->once())
            ->method('insertOnDuplicate')
            ->willThrowException(
                new LocalizedException(new Phrase('New error'))
            );
        $this->loggerMock->expects($this->once())
            ->method('critical')
            ->with('New error')
            ->willReturnSelf();
        $this->setUnlicensedImagesMediaGallery->execute($this->assetMock);
    }
}
