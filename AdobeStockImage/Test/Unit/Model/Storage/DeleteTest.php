<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Magento\AdobeStockImage\Test\Unit\Model\Storage;

use Exception;
use Magento\AdobeStockImage\Model\Storage\Delete;
use Magento\Cms\Helper\Wysiwyg\Images;
use Magento\Cms\Model\Wysiwyg\Images\Storage;
use Magento\Framework\Exception\CouldNotDeleteException;
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
     * @var Delete
     */
    private $delete;

    /**
     * @var MockObject|Storage
     */
    private $storageMock;

    /**
     * @var MockObject|LoggerInterface
     */
    private $logger;

    /**
     * @var Images|MockObject
     */
    private $imagesMock;

    /**
     * Initialize basic test object
     */
    protected function setUp(): void
    {
        $this->logger = $this->createMock(LoggerInterface::class);
        $this->storageMock = $this->createMock(Storage::class);
        $this->imagesMock = $this->createMock(Images::class);

        $this->delete = (new ObjectManager($this))->getObject(
            Delete::class,
            [
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
        $this->storageMock->expects($this->once())
            ->method('getCmsWysiwygImages')
            ->willReturn($this->imagesMock);
        $this->imagesMock->expects($this->once())
            ->method('getStorageRoot')
            ->willReturn($absolutePath);
        $this->storageMock->expects($this->once())
            ->method('deleteFile')
            ->with($absolutePath . $path)
            ->willReturn(true);

        $this->delete->execute($path);
    }

    /**
     * Assume that delete action will thrown an Exception
     *
     * @dataProvider getPathDataProvider
     * @param string $path
     * @param string $absolutePath
     * @return void
     */
    public function testExceptionOnDeleteExecution(string $path, string $absolutePath): void
    {
        $this->storageMock->expects($this->once())
            ->method('getCmsWysiwygImages')
            ->willReturn($this->imagesMock);
        $this->imagesMock->expects($this->once())
            ->method('getStorageRoot')
            ->willReturn($absolutePath);
        $this->storageMock->expects($this->once())
            ->method('deleteFile')
            ->with($absolutePath . $path)
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
                '/home/instance/path/'
            ]
        ];
    }
}
