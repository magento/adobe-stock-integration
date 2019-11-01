<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magento\AdobeStockImage\Test\Unit\Model;

use Magento\Framework\App\Filesystem\DirectoryList;
use Magento\Framework\Exception\CouldNotSaveException;
use Magento\Framework\TestFramework\Unit\Helper\ObjectManager;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Magento\AdobeStockImage\Model\Storage\Save;

/**
 * Test for the storage save functionality
 */
class SaveTest extends TestCase
{
    /**
     * @var MockObject | \Magento\Framework\Filesystem\Directory\Write
     */
    private $mediaDirectoryMock;

    /**
     * @var Save
     */
    private $save;

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
    private $logger;

    /**
     * Initialize base test objects
     */
    public function setUp()
    {

        $this->fileSystemMock = $this->createMock(\Magento\Framework\Filesystem::class);
        $this->httpsDriverMock = $this->createMock(\Magento\Framework\Filesystem\Driver\Https::class);
        $this->fileSystemIoMock = $this->createMock(\Magento\Framework\Filesystem\Io\File::class);
        $this->logger = $this->createMock(\Psr\Log\LoggerInterface::class);
        $this->mediaDirectoryMock = $this->createMock(\Magento\Framework\Filesystem\Directory\Write::class);

        $this->save = (new ObjectManager($this))->getObject(
            Save::class,
            [
                'filesystem'   => $this->fileSystemMock,
                'driver'       => $this->httpsDriverMock,
                'fileSystemIo' => $this->fileSystemIoMock,
                'logger'          => $this->logger,
            ]
        );
    }

    /**
     * Test image preview save
     */
    public function testSavePreview()
    {
        $imageUrl = 'https://t4.ftcdn.net/jpg/02/72/29/99/240_F_272299924_HjNOJkyyhzFVKRcSQ2TaArR7Ka6nTXRa.jpg';

        $this->fileSystemMock->expects($this->once())
            ->method('getDirectoryWrite')
            ->with(DirectoryList::MEDIA)
            ->willReturn($this->mediaDirectoryMock);

        $content = 'content';
        $this->httpsDriverMock->expects($this->once())
            ->method('fileGetContents')
            ->willReturn($content);

        $this->mediaDirectoryMock->expects($this->once())
            ->method('writeFile')
            ->withAnyParameters()
            ->willReturn($this->isType('integer'));

        $this->assertSame(
            '240_F_272299924_HjNOJkyyhzFVKRcSQ2TaArR7Ka6nTXRa.jpg',
            $this->save->execute($imageUrl, '240_F_272299924_HjNOJkyyhzFVKRcSQ2TaArR7Ka6nTXRa.jpg')
        );
    }

    /**
     * Assume that save action will thrown an Exception
     */
    public function testExceptionOnSaveExecution()
    {
        $imageUrl = 'https://t4.ftcdn.net/jpg/02/72/29/99/240_F_272299924_HjNOJkyyhzFVKRcSQ2TaArR7Ka6nTXRa.jpg';

        $this->fileSystemMock->expects($this->once())
            ->method('getDirectoryWrite')
            ->with(DirectoryList::MEDIA)
            ->willReturn($this->mediaDirectoryMock);

        $content = 'content';
        $this->httpsDriverMock->expects($this->once())
            ->method('fileGetContents')
            ->willReturn($content);

        $this->mediaDirectoryMock->expects($this->once())
            ->method('writeFile')
            ->withAnyParameters()
            ->willThrowException(new \Exception());

        $this->expectException(CouldNotSaveException::class);
        $this->logger->expects($this->once())
            ->method('critical')
            ->willReturnSelf();

        $this->save->execute($imageUrl, '240_F_272299924_HjNOJkyyhzFVKRcSQ2TaArR7Ka6nTXRa.jpg');
    }
}
