<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magento\AdobeStockImage\Test\Unit\Model;

use Magento\Framework\TestFramework\Unit\Helper\ObjectManager;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

/**
 * Test for Storage Model
 */
class StorageTest extends TestCase
{
    /**
     * @var Storage
     */
    private $storage;

    /**
     * @var MockObject | \Magento\Framework\Filesystem
     */
    public $fileSystemMock;

    /**
     * @var MockObject | \Magento\Framework\Filesystem\DriverInterface
     */
    private $httpsDriverMock;

    /**
     * @var MockObject | \Magento\Framework\Filesystem\Io\File
     */
    private $fileSystemIoMock;

    /**
     * @var MockObject | \Psr\Log\LoggerInterface
     */
    private $logMock;

    public function setUp()
    {

        $this->fileSystemMock = $this->createMock(\Magento\Framework\Filesystem::class);
        $this->httpsDriverMock = $this->createMock(\Magento\Framework\Filesystem\Driver\Https::class);
        $this->fileSystemIoMock = $this->createMock(\Magento\Framework\Filesystem\Io\File::class);
        $this->logMock = $this->createMock(\Psr\Log\LoggerInterface::class);

        $this->storage = (new ObjectManager($this))->getObject(
            Storage::class,
            [
                'filesystem'   => $this->fileSystemMock,
                'driver'       => $this->httpsDriverMock,
                'fileSystemIo' => $this->fileSystemIoMock,
                'log'          => $this->logMock,
            ]
        );
    }

    public function testSavePreview()
    {
        /** @var Storage $storageMock */

        $imageUrl = 'https://t4.ftcdn.net/jpg/02/72/29/99/240_F_272299924_HjNOJkyyhzFVKRcSQ2TaArR7Ka6nTXRa.jpg';

        $this->fileSystemIoMock->expects($this->once())
            ->method('getPathInfo')
            ->with($imageUrl)
            ->willReturn(
                [
                    'dirname'   => 'https://t4.ftcdn.net/jpg/02/72/29/99',
                    'basename'  => '240_F_272299924_HjNOJkyyhzFVKRcSQ2TaArR7Ka6nTXRa.jpg',
                    'extension' => 'jpg',
                    'filename'  => '240_F_272299924_HjNOJkyyhzFVKRcSQ2TaArR7Ka6nTXRa',
                ]
            );
        $mediaDirectoryMock = $this->createMock(\Magento\Framework\Filesystem\Directory\Write::class);

        $content = 'content';
        $mediaDirectoryMock->expects($this->once())
            ->method('writeFile')
            ->withAnyParameters()
            ->willReturn($this->isType('integer'));

        $this->fileSystemMock->expects($this->once())
            ->method('getDirectoryWrite')
            ->with(\Magento\Framework\App\Filesystem\DirectoryList::MEDIA)
            ->willReturn($mediaDirectoryMock);

        $this->httpsDriverMock->expects($this->once())
            ->method('fileGetContents')
            ->willReturn($content);

        $this->assertSame('240_F_272299924_HjNOJkyyhzFVKRcSQ2TaArR7Ka6nTXRa.jpg', $this->storage->save($imageUrl));
    }
}
