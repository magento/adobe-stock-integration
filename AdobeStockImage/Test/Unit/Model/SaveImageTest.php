<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magento\AdobeStockImage\Test\Unit\Model;

use Magento\AdobeStockAsset\Model\SaveAsset;
use Magento\AdobeStockClientApi\Api\ClientInterface;
use Magento\AdobeStockImage\Model\SaveImage;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Magento\AdobeStockImage\Model\Storage;
use Psr\Log\LoggerInterface;

/**
 * Test for Save image model.
 */
class SaveImageTest extends TestCase
{
    /**
     * @var MockObject|ClientInterface
     */
    private $client;

    /**
     * @var MockObject|SaveAsset
     */
    private $saveAsset;

    /**
     * @var MockObject|Storage $storage
     */
    private $storage;

    /**
     * @var MockObject|LoggerInterface $logger
     */
    private $logger;

    /**
     * @var SaveImage $saveImage
     */
    private $saveImage;

    /**
     * @inheritDoc
     */
    public function setUp()
    {
        $this->saveAsset = $this->createMock(SaveAsset::class);
        $this->storage = $this->createMock(Storage::class);
        $this->logger = $this->createMock(LoggerInterface::class);
        $this->client = $this->createMock(ClientInterface::class);

        $this->saveImage = new SaveImage(
            $this->saveAsset,
            $this->storage,
            $this->logger,
            $this->client
        );
    }

    /**
     * Verify that image can be saved.
     */
    public function testExecute()
    {
        $asset = $this->createMock(\Magento\AdobeStockAssetApi\Api\Data\AssetInterface::class);

        $this->storage->expects($this->once())->method('save')
            ->willReturn('');
        $asset->expects($this->once())->method('getUrl')
            ->willReturn('https://as2.ftcdn.net/jpg/500_FemVonDcttCeKiOXFk.jpg');
        $asset->expects($this->once())->method('setPath')->willReturn(null);
        $this->saveAsset->expects($this->once())->method('execute')
            ->with($asset);
        $this->saveImage->execute($asset, '');
    }
}
