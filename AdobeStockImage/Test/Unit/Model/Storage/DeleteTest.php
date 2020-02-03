<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Magento\AdobeStockImage\Test\Unit\Model\Storage;

use Exception;
use Magento\AdobeStockImage\Model\Storage\Delete;
use Magento\Cms\Model\Wysiwyg\Images\Storage;
use Magento\Framework\App\Filesystem\DirectoryList;
use Magento\Framework\Exception\CouldNotDeleteException;
use Magento\Framework\Filesystem;
use Magento\Framework\Filesystem\Directory\Write;
use Magento\Framework\TestFramework\Unit\Helper\ObjectManager;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Psr\Log\LoggerInterface;

/**
 * Test for the storage delete functionality
 */
class DeleteTest extends TestCase
{
    /**
     * @var MockObject|Write
     */
    private $mediaDirectoryMock;

    /**
     * @var Delete
     */
    private $delete;

    /**
     * @var MockObject|Filesystem
     */
    private $fileSystemMock;

    /**
     * @var MockObject|Storage
     */
    private $storageMock;

    /**
     * @var MockObject|LoggerInterface
     */
    private $logger;

    /**
     * Initialize basic test object
     */
    protected function setUp(): void
    {
        $this->fileSystemMock = $this->createMock(Filesystem::class);
        $this->logger = $this->createMock(LoggerInterface::class);
        $this->mediaDirectoryMock = $this->createMock(Write::class);
        $this->storageMock = $this->createMock(Storage::class);

        $this->delete = (new ObjectManager($this))->getObject(
            Delete::class,
            [
                'filesystem' => $this->fileSystemMock,
                'logger' => $this->logger,
                'storage' => $this->storageMock
            ]
        );
    }

    /**
     * Test storage delete action
     *
     * @param string $path
     * @param string $absolutePath
     * @dataProvider getPathDataProvider
     * @return void
     */
    public function testExecute(string $path, string $absolutePath): void
    {
        $path = 'path';
        $absolutePath = '/home/instance/path';

        $this->fileSystemMock->expects($this->once())
            ->method('getDirectoryWrite')
            ->with(DirectoryList::MEDIA)
            ->willReturn($this->mediaDirectoryMock);
        $this->mediaDirectoryMock->expects($this->once())
            ->method('isFile')
            ->with($path)
            ->willReturn(true);
        $this->mediaDirectoryMock->expects($this->once())
            ->method('getAbsolutePath')
            ->with($path)
            ->willReturn($absolutePath);
        $this->storageMock->expects($this->once())
            ->method('deleteFile')
            ->with($absolutePath)
            ->willReturn(true);

        $this->delete->execute($path);
    }

    /**
     * Assume that delete action wilyyl thrown an Exception
     *
     * @dataProvider getPathDataProvider
     * @param string $path
     * @param string $absolutePath
     * @return void
     */
    public function testExceptionOnDeleteExecution(string $path, string $absolutePath): void
    {
        $this->fileSystemMock->expects($this->once())
            ->method('getDirectoryWrite')
            ->with(DirectoryList::MEDIA)
            ->willReturn($this->mediaDirectoryMock);
        $this->mediaDirectoryMock->expects($this->once())
            ->method('isFile')
            ->with($path)
            ->willReturn(true);
        $this->mediaDirectoryMock->expects($this->once())
            ->method('getAbsolutePath')
            ->with($path)
            ->willReturn($absolutePath);
        $this->storageMock->expects($this->once())
            ->method('deleteFile')
            ->with($absolutePath)
            ->willThrowException(new Exception());

        $this->expectException(CouldNotDeleteException::class);

        $this->logger->expects($this->once())
            ->method('critical')
            ->willReturnSelf();

        $this->delete->execute($path);
    }

    /**
     * Path data provider for tests
     *
     * @return array
     */
    public function getPathDataProvider(): array
    {
        return [
            [
                'path',
                '/home/instance/path'
            ]
        ];
    }
}
