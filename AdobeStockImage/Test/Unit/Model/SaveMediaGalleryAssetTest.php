<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Magento\AdobeStockImage\Test\Unit\Model;

use Magento\AdobeStockImage\Model\Extract\MediaGalleryAsset as DocumentToMediaGalleryAsset;
use Magento\AdobeStockImage\Model\SaveMediaGalleryAsset;
use Magento\Framework\Api\Search\Document;
use Magento\Framework\App\Filesystem\DirectoryList;
use Magento\Framework\Exception\CouldNotSaveException;
use Magento\Framework\Filesystem;
use Magento\Framework\Filesystem\Directory\Read;
use Magento\Framework\TestFramework\Unit\Helper\ObjectManager;
use Magento\MediaGallery\Model\Asset;
use Magento\MediaGalleryApi\Model\Asset\Command\SaveInterface;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

/**
 * Test saving a media gallery asset and return its id.
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class SaveMediaGalleryAssetTest extends TestCase
{
    /**
     * @var SaveInterface|MockObject
     */
    private $saveMediaAssetMock;

    /**
     * @var DocumentToMediaGalleryAsset|MockObject
     */
    private $documentToMediaGalleryAssetMock;

    /**
     * @var FileSystem|MockObject
     */
    private $fileSystemMock;

    /**
     * @var Read|MockObject
     */
    private $mediaDirectoryMock;

    /**
     * @var SaveMediaGalleryAsset
     */
    private $saveMediaAsset;

    /**
     * @inheritdoc
     */
    protected function setUp(): void
    {
        $this->saveMediaAssetMock = $this->createMock(SaveInterface::class);
        $this->documentToMediaGalleryAssetMock = $this->createMock(DocumentToMediaGalleryAsset::class);
        $this->fileSystemMock = $this->createMock(Filesystem::class);
        $this->mediaDirectoryMock = $this->createMock(Read::class);

        $this->saveMediaAsset = (new ObjectManager($this))->getObject(
            SaveMediaGalleryAsset::class,
            [
                'saveMediaAsset' =>  $this->saveMediaAssetMock,
                'documentToMediaGalleryAsset' =>  $this->documentToMediaGalleryAssetMock,
                'fileSystem' => $this->fileSystemMock
            ]
        );
        $reflection = new \ReflectionClass(get_class($this->saveMediaAsset));
        $reflectionMethod = $reflection->getMethod('calculateFileSize');
        $reflectionMethod->setAccessible(true);
    }

    /**
     * Verify successful save of a media gallery asset id.
     *
     * @param Document $document
     * @param string $destinationPath
     * @param int $mediaGalleryAssetId
     * @param bool $isMediaAssetExists
     *
     * @dataProvider assetProvider
     */
    public function testExecute(
        Document $document,
        string $destinationPath,
        int $mediaGalleryAssetId,
        bool $isMediaAssetExists
    ): void {
        $this->fileSystemMock->expects($this->once())
            ->method('getDirectoryRead')
            ->with(DirectoryList::MEDIA)
            ->willReturn($this->mediaDirectoryMock);

        $this->mediaDirectoryMock->expects($this->once())
            ->method('getAbsolutePath')
            ->with($destinationPath)
            ->willReturn('root/pub/media/catalog/test-image.jpeg');

        $fileSize = 42;
        $this->mediaDirectoryMock->expects($this->once())
            ->method('stat')
            ->willReturn(['size' => $fileSize]);

        $additionalData = [
            'id' => null,
            'path' => $destinationPath,
            'source' => 'Adobe Stock',
            'size' => $fileSize,
        ];
        $mediaGalleryAssetMock = $this->createMock(Asset::class);
        $this->documentToMediaGalleryAssetMock->expects($this->once())
            ->method('convert')
            ->with($document, $additionalData)
            ->willReturn($mediaGalleryAssetMock);

        $isMediaAssetExists ?
            $this->saveMediaAssetMock->expects($this->once())
                ->method('execute')
                ->with($mediaGalleryAssetMock)
                ->willReturn($mediaGalleryAssetId)
            : $this->saveMediaAssetMock->expects($this->once())
                ->method('execute')
                ->with($mediaGalleryAssetMock)
                ->willReturn(0);

        $this->saveMediaAsset->execute($document, $destinationPath);
    }

    /**
     * Test save media gallery asset with exception.
     *
     * @param Document $document
     * @param string $destinationPath
     * @dataProvider assetProvider
     */
    public function testExecuteWithException(
        Document $document,
        string $destinationPath
    ): void {
        $this->fileSystemMock->expects($this->once())
            ->method('getDirectoryRead')
            ->with(DirectoryList::MEDIA)
            ->willThrowException(new \Exception('Some Exception'));

        $this->expectException(CouldNotSaveException::class);

        $this->saveMediaAsset->execute($document, $destinationPath);
    }

    /**
     * Data provider for testExecute
     *
     * @return array
     */
    public function assetProvider(): array
    {
        return [
            [
                'document' => $this->getDocument(),
                'destinationPath' => 'path',
                'mediaGalleryAssetId' => 12345,
                'isMediaAssetExists' => true
            ],
            [
                'document' => $this->getDocument(),
                'destinationPath' => 'path',
                'mediaGalleryAssetId' => 12345,
                'isMediaAssetExists' => false
            ]
        ];
    }

    /**
     * Get document
     *
     * @return MockObject
     */
    private function getDocument(): MockObject
    {
        return $this->createMock(Document::class);
    }
}
