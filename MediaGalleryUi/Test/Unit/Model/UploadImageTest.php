<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Magento\MediaGalleryUi\Test\Unit\Model;

use Magento\Cms\Model\Wysiwyg\Images\Storage;
use Magento\Framework\App\Filesystem\DirectoryList;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Filesystem;
use Magento\Framework\Filesystem\Directory\Read;
use Magento\Framework\TestFramework\Unit\Helper\ObjectManager;
use Magento\MediaGallerySynchronizationApi\Api\SynchronizeFilesInterface;
use Magento\MediaGallerySynchronization\Model\Filesystem\SplFileInfoFactory;
use Magento\MediaGalleryUi\Model\UploadImage;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

/**
 * Provides test for upload image functionality
 */
class UploadImageTest extends TestCase
{
    /**
     * @var Storage|MockObject
     */
    private $imagesStorageMock;

    /**
     * @var Filesystem|MockObject
     */
    private $fileSystemMock;

    /**
     * @var SplFileInfoFactory|MockObject
     */
    private $splFileInfoFactoryMock;

    /**
     * @var Read|MockObject
     */
    private $mediaDirectoryMock;

    /**
     * @var SynchronizeFilesInterface|MockObject
     */
    private $synchronizeFilesMock;

    /**
     * @var UploadImage
     */
    private $uploadImage;

    /**
     * Prepare test objects.
     */
    protected function setUp(): void
    {
        $this->imagesStorageMock = $this->createMock(Storage::class);
        $this->fileSystemMock = $this->createMock(Filesystem::class);
        $this->splFileInfoFactoryMock = $this->createMock(SplFileInfoFactory::class);
        $this->mediaDirectoryMock = $this->createMock(Read::class);
        $this->synchronizeFilesMock = $this->createMock(SynchronizeFilesInterface::class);

        $this->uploadImage = (new ObjectManager($this))->getObject(
            UploadImage::class,
            [
                'imagesStorage' => $this->imagesStorageMock,
                'splFileInfoFactory' => $this->splFileInfoFactoryMock,
                'filesystem' => $this->fileSystemMock,
                'synchronizeFiles' => $this->synchronizeFilesMock
            ]
        );
    }

    /**
     * Test successful image file upload.
     *
     * @param string $targetFolder
     * @param string|null $type
     * @param string $absolutePath
     *
     * @dataProvider executeDataProvider
     */
    public function testExecute(string $targetFolder, string $type = null, string $absolutePath): void
    {
        $this->fileSystemMock->expects($this->once())
            ->method('getDirectoryRead')
            ->with(DirectoryList::MEDIA)
            ->willReturn($this->mediaDirectoryMock);

        $this->mediaDirectoryMock->expects($this->once())
            ->method('isDirectory')
            ->with($targetFolder)
            ->willReturn(true);

        $this->mediaDirectoryMock->expects($this->once())
            ->method('getAbsolutePath')
            ->with($targetFolder)
            ->willReturn($absolutePath);

        $uploadResult = ['path' => 'media/catalog', 'file' => 'test-image.jpeg'];
        $this->imagesStorageMock->expects($this->once())
            ->method('uploadFile')
            ->with($absolutePath, $type)
            ->willReturn($uploadResult);

        $fileInfoMock = $this->createMock(\SplFileInfo::class);
        $this->splFileInfoFactoryMock->expects($this->once())
            ->method('create')
            ->with($uploadResult['path'] . '/' . $uploadResult['file'])
            ->willReturn($fileInfoMock);

        $this->uploadImage->execute($targetFolder, $type);
    }

    /**
     * Test upload image method with logical exception when the folder is not a folder.
     */
    public function testExecuteWithException(): void
    {
        $targetFolder = 'not-a-folder';
        $this->fileSystemMock->expects($this->once())
            ->method('getDirectoryRead')
            ->with(DirectoryList::MEDIA)
            ->willReturn($this->mediaDirectoryMock);

        $this->mediaDirectoryMock->expects($this->once())
            ->method('isDirectory')
            ->with($targetFolder)
            ->willReturn(false);

        $this->expectException(LocalizedException::class);
        $this->expectExceptionMessage('Directory not-a-folder does not exist in media directory.');

        $this->uploadImage->execute($targetFolder, null);
    }

    /**
     * Provides test case data.
     *
     * @return array
     */
    public function executeDataProvider(): array
    {
        return [
            [
                'targetFolder' => 'media/catalog',
                'type' => null,
                'absolutePath' => 'root/pub/media/catalog/test-image.jpeg'
            ]
        ];
    }
}
