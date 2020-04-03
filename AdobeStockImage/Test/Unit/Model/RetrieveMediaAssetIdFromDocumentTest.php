<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Magento\AdobeStockImage\Test\Unit\Model;

use Magento\AdobeStockAssetApi\Api\AssetRepositoryInterface;
use Magento\AdobeStockAssetApi\Api\Data\AssetInterface;
use Magento\AdobeStockImage\Model\Extract\MediaGalleryAsset as DocumentToMediaGalleryAsset;
use Magento\AdobeStockImage\Model\RetrieveMediaAssetIdFromDocument;
use Magento\AdobeStockImage\Model\RetrieveMediaAssetIdFromDocumentInterface;
use Magento\Framework\Api\Search\Document;
use Magento\Framework\App\Filesystem\DirectoryList;
use Magento\Framework\Filesystem;
use Magento\Framework\Filesystem\Directory\Read;
use Magento\Framework\Filesystem\Directory\ReadInterface;
use Magento\Framework\TestFramework\Unit\Helper\ObjectManager;
use Magento\MediaGallery\Model\Asset;
use Magento\MediaGalleryApi\Model\Asset\Command\SaveInterface;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

/**
 * Test saving a media gallery asset and file related to it.
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class RetrieveMediaAssetIdFromDocumentTest extends TestCase
{
    /**
     * @var RetrieveMediaAssetIdFromDocumentInterface|MockObject
     */
    private $saveMediaAssetMock;

    /**
     * @var DocumentToMediaGalleryAsset|MockObject
     */
    private $documentToMediaGalleryAssetMock;

    /**
     * @var AssetRepositoryInterface|MockObject
     */
    private $assetRepositoryMock;

    /**
     * @var FileSystem|MockObject
     */
    private $fileSystemMock;

    /**
     * @var ReadInterface|MockObject
     */
    private $readInterfaceMock;

    /**
     * @var Read|MockObject
     */
    private $mediaDirectoryMock;

    /**
     * @var RetrieveMediaAssetIdFromDocumentInterface
     */
    private $retrieveMediaAssetIdFromDocument;

    /**
     * @inheritdoc
     */
    protected function setUp(): void
    {
        $this->saveMediaAssetMock = $this->createMock(SaveInterface::class);
        $this->documentToMediaGalleryAssetMock = $this->createMock(DocumentToMediaGalleryAsset::class);
        $this->assetRepositoryMock = $this->createMock(AssetRepositoryInterface::class);
        $this->fileSystemMock = $this->createMock(Filesystem::class);
        $this->readInterfaceMock = $this->createMock(ReadInterface::class);
        $this->mediaDirectoryMock = $this->createMock(Read::class);

        $this->retrieveMediaAssetIdFromDocument = (new ObjectManager($this))->getObject(
            RetrieveMediaAssetIdFromDocument::class,
            [
                'saveMediaAsset' =>  $this->saveMediaAssetMock,
                'documentToMediaGalleryAsset' =>  $this->documentToMediaGalleryAssetMock,
                'assetRepository' => $this->assetRepositoryMock,
                'fileSystem' => $this->fileSystemMock
            ]
        );
        $reflection = new \ReflectionClass(get_class($this->retrieveMediaAssetIdFromDocument));
        $reflectionMethod = $reflection->getMethod('calculateFileSize');
        $reflectionMethod->setAccessible(true);
    }

    /**
     * Verify getting media gallery asset id from documnt after asset save.
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

        if (!$isMediaAssetExists) {
            $adobeStockAssetMock = $this->createMock(AssetInterface::class);
            $this->assetRepositoryMock->expects($this->once())
                ->method('getById')
                ->with($document->getId())
                ->willReturn($adobeStockAssetMock);
            $adobeStockAssetMock->expects($this->once())
                ->method('getMediaGalleryId')
                ->willReturn($mediaGalleryAssetId);
        }

        $this->assertEquals(
            $mediaGalleryAssetId,
            $this->retrieveMediaAssetIdFromDocument->execute($document, $destinationPath)
        );
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
                'document' => $this->getDocument(12345),
                'destinationPath' => 'path',
                'mediaGalleryAssetId' => 12345,
                'isMediaAssetExists' => false
            ]
        ];
    }

    /**
     * Get document
     *
     * @param int|null $assetId
     *
     * @return MockObject
     */
    private function getDocument(int $assetId = null): MockObject
    {
        $document = $this->createMock(Document::class);
        if ($assetId) {
            $document->method('getId')
                ->willReturn($assetId);
        }

        return $document;
    }
}
