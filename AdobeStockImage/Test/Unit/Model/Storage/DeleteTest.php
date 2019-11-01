<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magento\AdobeStockImage\Test\Unit\Model;

use Magento\Framework\App\Filesystem\DirectoryList;
use Magento\Framework\Exception\CouldNotDeleteException;
use Magento\Framework\TestFramework\Unit\Helper\ObjectManager;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Magento\AdobeStockImage\Model\Storage\Delete;

/**
 * Test for the storage delete functionality
 */
class DeleteTest extends TestCase
{
    /**
     * @var MockObject | \Magento\Framework\Filesystem\Directory\Write
     */
    private $mediaDirectoryMock;

    /**
     * @var Delete
     */
    private $delete;

    /**
     * @var MockObject | \Magento\Framework\Filesystem
     */
    public $fileSystemMock;

    /**
     * @var MockObject | \Magento\Framework\Filesystem\DriverInterface
     */
    private $httpsDriverMock;

    /**
     * @var MockObject | \Psr\Log\LoggerInterface
     */
    private $logger;

    /**
     * Initialize basic test object
     */
    public function setUp()
    {

        $this->fileSystemMock = $this->createMock(\Magento\Framework\Filesystem::class);
        $this->httpsDriverMock = $this->createMock(\Magento\Framework\Filesystem\Driver\Https::class);
        $this->logger = $this->createMock(\Psr\Log\LoggerInterface::class);
        $this->mediaDirectoryMock = $this->createMock(\Magento\Framework\Filesystem\Directory\Write::class);

        $this->delete = (new ObjectManager($this))->getObject(
            Delete::class,
            [
                'filesystem'   => $this->fileSystemMock,
                'driver'       => $this->httpsDriverMock,
                'logger'          => $this->logger,
            ]
        );
    }

    /**
     * Test storage delete action
     */
    public function testExecute()
    {
        $path = 'path';

        $this->fileSystemMock->expects($this->once())
            ->method('getDirectoryWrite')
            ->with(DirectoryList::MEDIA)
            ->willReturn($this->mediaDirectoryMock);

        $this->mediaDirectoryMock->expects($this->once())
            ->method('isFile')
            ->with($path)
            ->willReturn(true);

        $this->mediaDirectoryMock->expects($this->once())
            ->method('delete')
            ->with($path)
            ->willReturn(true);

        $this->delete->execute($path);
    }

    /**
     * Assume that delete action will thrown an Exception
     */
    public function testExceptionOnDeleteExecution()
    {
        $path = 'path';

        $this->fileSystemMock->expects($this->once())
            ->method('getDirectoryWrite')
            ->with(DirectoryList::MEDIA)
            ->willReturn($this->mediaDirectoryMock);

        $this->mediaDirectoryMock->expects($this->once())
            ->method('isFile')
            ->with($path)
            ->willReturn(true);

        $this->mediaDirectoryMock->expects($this->once())
            ->method('delete')
            ->with($path)
            ->willThrowException(new \Exception());

        $this->expectException(CouldNotDeleteException::class);
        $this->logger->expects($this->once())
            ->method('critical')
            ->willReturnSelf();

        $this->delete->execute($path);
    }
}
