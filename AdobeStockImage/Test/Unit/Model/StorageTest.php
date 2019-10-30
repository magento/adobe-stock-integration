<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magento\AdobeStockImage\Test\Unit\Model;

use Magento\Framework\App\Filesystem\DirectoryList;
use Magento\Framework\TestFramework\Unit\Helper\ObjectManager;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Magento\AdobeStockImage\Model\Storage;

/**
 * Test for Storage Model
 */
class StorageTest extends TestCase
{
    /**
     * @var MockObject | \Magento\Framework\Filesystem\Directory\Write
     */
    private $mediaDirectoryMock;

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
        $this->mediaDirectoryMock = $this->createMock(\Magento\Framework\Filesystem\Directory\Write::class);

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

        $content = 'content';
        $this->mediaDirectoryMock->expects($this->once())
            ->method('writeFile')
            ->withAnyParameters()
            ->willReturn($this->isType('integer'));

        $this->fileSystemMock->expects($this->once())
            ->method('getDirectoryWrite')
            ->with(DirectoryList::MEDIA)
            ->willReturn($this->mediaDirectoryMock);

        $this->httpsDriverMock->expects($this->once())
            ->method('fileGetContents')
            ->willReturn($content);

        $this->assertSame(
            '240_F_272299924_HjNOJkyyhzFVKRcSQ2TaArR7Ka6nTXRa.jpg',
            $this->storage->save($imageUrl, '240_F_272299924_HjNOJkyyhzFVKRcSQ2TaArR7Ka6nTXRa.jpg')
        );
    }

    public function testDelete()
    {
        $path = 'path';

        $this->mediaDirectoryMock->expects($this->once())
            ->method('isFile')
            ->with($path)
            ->willReturn(true);

        $this->fileSystemMock->expects($this->once())
            ->method('getDirectoryWrite')
            ->with(DirectoryList::MEDIA)
            ->willReturn($this->mediaDirectoryMock);

        $this->mediaDirectoryMock->expects($this->once())
            ->method('delete')
            ->with($path)
            ->willReturn(true);

        $this->storage->delete($path);
    }
}
