<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magento\AdobeStockImage\Test\Unit\Model;

use Magento\AdobeStockAssetApi\Api\Data\AssetInterface;
use Magento\AdobeStockAssetApi\Api\SaveAssetInterface;
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
     * @var MockObject|SaveAssetInterface
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
        $this->saveAsset = $this->getMockForAbstractClass(SaveAssetInterface::class);
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
     * @param int $isLicensed
     * @param string $path
     * @param string $url
     * @throws \Magento\Framework\Exception\CouldNotSaveException
     * @dataProvider assetProvider
     */
    public function testExecute(int $isLicensed, string $path, string $url)
    {
        $this->storage->expects($this->once())
            ->method('save')
            ->willReturn($path);

        if (!empty($path)) {
            $this->storage->expects($this->once())
                ->method('delete');
        } else {
            $this->storage->expects($this->never())
                ->method('delete');
        }

        $asset = $this->getAsset($isLicensed, $path, $url);

        $this->saveAsset->expects($this->once())
            ->method('execute')
            ->with($asset);
        $this->saveImage->execute($asset, $path);
    }

    /**
     * @param int $isLicensed
     * @param string $path
     * @param string $url
     * @return AssetInterface|MockObject
     */
    private function getAsset(int $isLicensed, string $path, string $url): AssetInterface
    {
        $asset = $this->createMock(AssetInterface::class);

        $asset->expects($this->once())
            ->method('isLicensed')
            ->willReturn($isLicensed);

        if ($isLicensed) {
            $asset->expects($this->any())
                ->method('getUrl')
                ->willReturn($url);
            $asset->expects($this->never())
                ->method('getPreviewUrl');
        } else {
            $asset->expects($this->never())
                ->method('getUrl');
            $asset->expects($this->once())
                ->method('getPreviewUrl')
                ->willReturn($url);
        }

        $asset->expects($this->once())
            ->method('setPath')
            ->with($path)
            ->willReturn(null);

        $asset->expects($this->atLeast(1))
            ->method('getPath')
            ->willReturn($path);

        return $asset;
    }

    /**
     * Data provider for testExecute
     *
     * @return array
     */
    public function assetProvider(): array
    {
        return [
            'licensed asset' => [
                'isLicensed' => 1,
                'path' => 'path',
                'url' => 'https://as2.ftcdn.net/jpg/500_FemVonDcttCeKiOXFk.jpg'
            ],
            'preview asset' => [
                'isLicensed' => 0,
                'path' => 'path',
                'url' => 'https://as2.ftcdn.net/jpg/500_FemVonDcttCeKiOXFk.jpg'
            ],
            'asset with no path' => [
                'isLicensed' => 0,
                'path' => '',
                'url' => 'https://as2.ftcdn.net/jpg/500_FemVonDcttCeKiOXFk.jpg'
            ],
        ];
    }
}
