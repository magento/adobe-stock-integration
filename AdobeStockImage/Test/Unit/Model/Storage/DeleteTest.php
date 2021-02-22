<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Magento\AdobeStockImage\Test\Unit\Model\Storage;

use Exception;
use Magento\Framework\App\Filesystem\DirectoryList;
use Magento\Framework\Exception\CouldNotDeleteException;
use Magento\Framework\Filesystem;
use Magento\Framework\Filesystem\Directory\Write;
use Magento\Framework\TestFramework\Unit\Helper\ObjectManager;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Magento\AdobeStockImage\Model\Storage\Delete;
use Psr\Log\LoggerInterface;

/**
 * Test for the storage delete functionality
 */
class DeleteTest extends TestCase
{
    /**
     * @var MockObject | Write
     */
    private $mediaDirectoryMock;

    /**
     * @var Delete
     */
    private $delete;

    /**
     * @var MockObject | Filesystem
     */
    public $fileSystemMock;

    /**
     * @var MockObject | LoggerInterface
     */
    private $logger;

    /**
     * Initialize basic test object
     */
    protected function setUp(): void
    {
        $this->fileSystemMock = $this->createMock(Filesystem::class);
        $this->logger = $this->getMockForAbstractClass(LoggerInterface::class);
        $this->mediaDirectoryMock = $this->createMock(Write::class);

        $this->delete = (new ObjectManager($this))->getObject(
            Delete::class,
            [
                'filesystem'   => $this->fileSystemMock,
                'logger'          => $this->logger,
            ]
        );
    }

    /**
     * Test storage delete action
     */
    public function testExecute(): void
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
    public function testExceptionOnDeleteExecution(): void
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
            ->willThrowException(new Exception());

        $this->expectException(CouldNotDeleteException::class);
        $this->logger->expects($this->once())
            ->method('critical')
            ->willReturnSelf();

        $this->delete->execute($path);
    }
}
