<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Magento\AdobeStockImage\Test\Unit\Model\Storage;

use Magento\AdobeStockImage\Model\Storage\Save;
use Magento\Framework\App\Filesystem\DirectoryList;
use Magento\Framework\Exception\AlreadyExistsException;
use Magento\Framework\Filesystem;
use Magento\Framework\Filesystem\Directory\Write;
use Magento\Framework\Filesystem\Driver\Https;
use Magento\Framework\Filesystem\DriverInterface;
use Magento\Framework\Filesystem\Io\File;
use Magento\Framework\TestFramework\Unit\Helper\ObjectManager;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

/**
 * Test for the storage save functionality
 */
class SaveTest extends TestCase
{
    /**
     * @var MockObject | Write
     */
    private $mediaDirectoryMock;

    /**
     * @var Save
     */
    private $save;

    /**
     * @var MockObject | Filesystem
     */
    public $fileSystemMock;

    /**
     * @var MockObject | DriverInterface
     */
    private $httpsDriverMock;

    /**
     * @var MockObject | File
     */
    private $fileSystemIoMock;

    /**
     * Initialize base test objects
     */
    protected function setUp(): void
    {
        $this->fileSystemMock = $this->createMock(Filesystem::class);
        $this->httpsDriverMock = $this->createMock(Https::class);
        $this->fileSystemIoMock = $this->createMock(File::class);
        $this->mediaDirectoryMock = $this->createMock(Write::class);

        $this->save = (new ObjectManager($this))->getObject(
            Save::class,
            [
                'filesystem'   => $this->fileSystemMock,
                'driver'       => $this->httpsDriverMock,
                'fileSystemIo' => $this->fileSystemIoMock
            ]
        );
    }

    /**
     * Test image preview save
     */
    public function testSavePreview(): void
    {
        $imageUrl = 'https://t4.ftcdn.net/jpg/02/72/29/99/240_F_272299924_HjNOJkyyhzFVKRcSQ2TaArR7Ka6nTXRa.jpg';

        $this->fileSystemMock->expects($this->once())
            ->method('getDirectoryWrite')
            ->with(DirectoryList::MEDIA)
            ->willReturn($this->mediaDirectoryMock);

        $this->mediaDirectoryMock->expects($this->once())
            ->method('isExist')
            ->withAnyParameters()
            ->will($this->returnValue([]));
        $this->httpsDriverMock->expects($this->once())
            ->method('fileGetContents')
            ->willReturn('content');

        $this->mediaDirectoryMock->expects($this->once())
            ->method('writeFile')
            ->withAnyParameters()
            ->willReturn($this->isType('integer'));

        $this->assertSame(
            '/240_F_272299924_HjNOJkyyhzFVKRcSQ2TaArR7Ka6nTXRa.jpg',
            $this->save->execute($imageUrl, '/240_F_272299924_HjNOJkyyhzFVKRcSQ2TaArR7Ka6nTXRa.jpg')
        );
    }

    /**
     * Assume that save action will thrown an Exception
     */
    public function testExceptionOnSaveExecution(): void
    {
        $imageUrl = 'https://t4.ftcdn.net/jpg/02/72/29/99/240_F_272299924_HjNOJkyyhzFVKRcSQ2TaArR7Ka6nTXRa.jpg';

        $this->fileSystemMock->expects($this->once())
            ->method('getDirectoryWrite')
            ->with(DirectoryList::MEDIA)
            ->willReturn($this->mediaDirectoryMock);
        $this->mediaDirectoryMock->expects($this->once())
            ->method('isExist')
            ->withAnyParameters()
            ->will($this->returnValue(['240_F_272299924_HjNOJkyyhzFVKRcSQ2TaArR7Ka6nTXRa.jpg']));

        $this->httpsDriverMock->expects($this->never())
             ->method('fileGetContents');

        $this->mediaDirectoryMock->expects($this->never())
            ->method('writeFile');

        $this->expectException(AlreadyExistsException::class);

        $this->save->execute($imageUrl, '/240_F_272299924_HjNOJkyyhzFVKRcSQ2TaArR7Ka6nTXRa.jpg');
    }
}
