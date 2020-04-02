<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Magento\AdobeStockImage\Test\Unit\Model;

use Magento\AdobeStockAssetApi\Api\SaveAssetInterface;
use Magento\AdobeStockImage\Model\Extract\AdobeStockAsset as DocumentToAsset;
use Magento\AdobeStockImage\Model\Extract\Keywords as DocumentToKeywords;
use Magento\AdobeStockImage\Model\Extract\MediaGalleryAsset as DocumentToMediaGalleryAsset;
use Magento\AdobeStockImage\Model\SaveImageFile;
use Magento\AdobeStockImage\Model\SetLicensedInMediaGalleryGrid;
use Magento\AdobeStockImage\Model\Storage\Delete as StorageDelete;
use Magento\AdobeStockImage\Model\Storage\Save as StorageSave;
use Magento\Framework\Api\AttributeInterface;
use Magento\Framework\Api\Search\Document;
use Magento\Framework\App\Filesystem\DirectoryList;
use Magento\Framework\TestFramework\Unit\Helper\ObjectManager;
use Magento\MediaGalleryApi\Model\Asset\Command\SaveInterface;
use Magento\MediaGalleryApi\Model\Keyword\Command\SaveAssetKeywordsInterface;
use PHPUnit\Framework\MockObject\MockObject;
use Magento\Framework\Filesystem;
use Magento\Framework\Filesystem\Directory\ReadInterface;
use Magento\Framework\Filesystem\Directory\Read;
use PHPUnit\Framework\TestCase;
use Psr\Log\LoggerInterface;

/**
 * Test saving image file and create Media Gallery asset.
 */
class SaveImageFileTest extends TestCase
{
    /**
     * @var MockObject|StorageSave
     */
    private $storageSave;

    /**
     * @var MockObject|StorageDelete
     */
    private $storageDelete;

    /**
     * @var MockObject|LoggerInterface
     */
    private $logger;

    /**
     * @var MockObject|SaveInterface
     */
    private $saveMediaAsset;

    /**
     * @var MockObject|SaveAssetInterface
     */
    private $saveAdobeStockAsset;

    /**
     * @var MockObject|DocumentToMediaGalleryAsset
     */
    private $documentToMediaGalleryAsset;

    /**
     * @var MockObject|DocumentToAsset
     */
    private $documentToAsset;

    /**
     * @var MockObject|DocumentToKeywords
     */
    private $documentToKeywords;

    /**
     * @var MockObject|SaveAssetKeywordsInterface
     */
    private $saveAssetKeywords;

    /**
     * @var SetLicensedInMediaGalleryGrid|MockObject
     */
    private $setLicensedInMediaGalleryGridMock;

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
     * @var SaveImageFile
     */
    private $saveImageFile;

    /**
     * @inheritdoc
     */
    protected function setUp(): void
    {
        $this->storageSave = $this->createMock(StorageSave::class);
        $this->storageDelete = $this->createMock(StorageDelete::class);
        $this->logger = $this->createMock(LoggerInterface::class);
        $this->saveMediaAsset = $this->createMock(SaveInterface::class);
        $this->saveAdobeStockAsset = $this->createMock(SaveAssetInterface::class);
        $this->documentToMediaGalleryAsset = $this->createMock(DocumentToMediaGalleryAsset::class);
        $this->documentToAsset = $this->createMock(DocumentToAsset::class);
        $this->documentToKeywords = $this->createMock(DocumentToKeywords::class);
        $this->saveAssetKeywords = $this->createMock(SaveAssetKeywordsInterface::class);
        $this->setLicensedInMediaGalleryGridMock = $this->createMock(SetLicensedInMediaGalleryGrid::class);
        $this->fileSystemMock = $this->createMock(Filesystem::class);
        $this->readInterfaceMock = $this->createMock(ReadInterface::class);
        $this->mediaDirectoryMock = $this->createMock(Read::class);

        $this->saveImageFile = (new ObjectManager($this))->getObject(
            SaveImageFile::class,
            [
                'storageSave' => $this->storageSave,
                'storageDelete' => $this->storageDelete,
                'logger' => $this->logger,
                'saveMediaAsset' =>  $this->saveMediaAsset,
                'saveAdobeStockAsset' =>  $this->saveAdobeStockAsset,
                'documentToMediaGalleryAsset' =>  $this->documentToMediaGalleryAsset,
                'documentToAsset' =>  $this->documentToAsset,
                'documentToKeywords' => $this->documentToKeywords,
                'saveAssetKeywords' => $this->saveAssetKeywords,
                'setLicensedInMediaGalleryGrid' => $this->setLicensedInMediaGalleryGridMock,
                'fileSystem' => $this->fileSystemMock
            ]
        );
    }

    /**
     * Verify that image can be saved.
     *
     * @param Document $document
     * @param string $url
     * @param string $destinationPath
     * @param bool $delete
     * @dataProvider assetProvider
     */
    public function testExecute(Document $document, string $url, string $destinationPath, bool $delete): void
    {
        $path = 'catalog/test-image.jpeg';
        if ($delete) {
            $this->storageDelete->expects($this->once())
                ->method('execute');
        } else {
            $this->storageDelete->expects($this->never())
                ->method('execute');
        }

        $this->storageSave->expects($this->once())
            ->method('execute')
            ->willReturn($path);

        $this->fileSystemMock->expects($this->once())
            ->method('getDirectoryRead')
            ->with(DirectoryList::MEDIA)
            ->willReturn($this->mediaDirectoryMock);

        $this->mediaDirectoryMock->expects($this->once())
            ->method('getAbsolutePath')
            ->with($path)
            ->willReturn('root/pub/media/catalog/test-image.jpeg');

        $this->mediaDirectoryMock->expects($this->once())
            ->method('stat')
            ->willReturn(['size' => 12345]);

        $this->documentToMediaGalleryAsset->expects($this->once())
            ->method('convert')
            ->with($document);

        $mediaGalleryAssetId = 42;

        $this->saveMediaAsset->expects($this->once())
            ->method('execute')
            ->willReturn($mediaGalleryAssetId);

        $this->saveImageFile->execute($document, $url, $destinationPath);
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
                'url' => 'https://as2.ftcdn.net/jpg/500_FemVonDcttCeKiOXFk.jpg',
                'destinationPath' => 'path',
                'delete' => false
            ],
            [
                'document' => $this->getDocument('filepath.jpg'),
                'url' => 'https://as2.ftcdn.net/jpg/500_FemVonDcttCeKiOXFk.jpg',
                'destinationPath' => 'path',
                'delete' => true
            ],
        ];
    }

    /**
     * Get document
     *
     * @param string|null $path
     * @return MockObject
     */
    private function getDocument(?string $path = null): MockObject
    {
        $document = $this->createMock(Document::class);
        $pathAttribute = $this->createMock(AttributeInterface::class);
        $pathAttribute->expects($this->once())
            ->method('getValue')
            ->willReturn($path);
        $document->expects($this->once())
            ->method('getCustomAttribute')
            ->with('path')
            ->willReturn($pathAttribute);

        return $document;
    }
}
