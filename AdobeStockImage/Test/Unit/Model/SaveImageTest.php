<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Magento\AdobeStockImage\Test\Unit\Model;

use Magento\MediaGalleryApi\Model\Asset\Command\SaveInterface;
use Magento\MediaGalleryApi\Model\Keyword\Command\SaveAssetKeywordsInterface;
use Magento\AdobeStockAssetApi\Api\SaveAssetInterface;
use Magento\AdobeStockImage\Model\Extract\AdobeStockAsset as DocumentToAsset;
use Magento\AdobeStockImage\Model\Extract\Keywords as DocumentToKeywords;
use Magento\AdobeStockImage\Model\Extract\MediaGalleryAsset as DocumentToMediaGalleryAsset;
use Magento\AdobeStockImage\Model\SaveImage;
use Magento\AdobeStockImage\Model\Storage\Save as StorageSave;
use Magento\AdobeStockImage\Model\Storage\Delete as StorageDelete;
use Magento\Framework\Api\AttributeInterface;
use Magento\Framework\Api\Search\Document;
use Magento\Framework\Exception\CouldNotSaveException;
use Magento\Framework\TestFramework\Unit\Helper\ObjectManager;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Psr\Log\LoggerInterface;

/**
 * Test for Save image model.
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class SaveImageTest extends TestCase
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
     * @var SaveImage
     */
    private $saveImage;

    /**
     * @inheritdoc
     */
    protected function setUp(): void
    {
        $this->storageSave = $this->createMock(StorageSave::class);
        $this->storageDelete = $this->createMock(StorageDelete::class);
        $this->logger = $this->getMockForAbstractClass(LoggerInterface::class);
        $this->saveMediaAsset = $this->getMockForAbstractClass(SaveInterface::class);
        $this->saveAdobeStockAsset = $this->getMockForAbstractClass(SaveAssetInterface::class);
        $this->documentToMediaGalleryAsset = $this->createMock(DocumentToMediaGalleryAsset::class);
        $this->documentToAsset = $this->createMock(DocumentToAsset::class);
        $this->documentToKeywords = $this->createMock(DocumentToKeywords::class);
        $this->saveAssetKeywords = $this->getMockForAbstractClass(SaveAssetKeywordsInterface::class);

        $this->saveImage = (new ObjectManager($this))->getObject(
            SaveImage::class,
            [
                'storageSave' => $this->storageSave,
                'storageDelete' => $this->storageDelete,
                'logger' => $this->logger,
                'saveMediaAsset' =>  $this->saveMediaAsset,
                'saveAdobeStockAsset' =>  $this->saveAdobeStockAsset,
                'documentToMediaGalleryAsset' =>  $this->documentToMediaGalleryAsset,
                'documentToAsset' =>  $this->documentToAsset,
                'documentToKeywords' => $this->documentToKeywords,
                'saveAssetKeywords' => $this->saveAssetKeywords
            ]
        );
    }

    /**
     * Verify that image can be saved.
     *
     * @param Document $document
     * @param bool $delete
     * @throws CouldNotSaveException
     * @dataProvider assetProvider
     */
    public function testExecute(Document $document, bool $delete): void
    {
        if ($delete) {
            $this->storageDelete->expects($this->once())
                ->method('execute');
        } else {
            $this->storageDelete->expects($this->never())
                ->method('execute');
        }

        $this->storageSave->expects($this->once())
            ->method('execute');

        $this->documentToMediaGalleryAsset->expects($this->once())
            ->method('convert')
            ->with($document);

        $mediaGalleryAssetId = 42;

        $this->saveMediaAsset->expects($this->once())
            ->method('execute')
            ->willReturn($mediaGalleryAssetId);

        $this->documentToKeywords->expects($this->once())
            ->method('convert')
            ->with($document);

        $this->saveAssetKeywords->expects($this->once())
            ->method('execute');

        $this->documentToAsset->expects($this->once())
            ->method('convert')
            ->with($document);

        $this->saveAdobeStockAsset->expects($this->once())
            ->method('execute');

        $this->saveImage->execute($document, 'https://as2.ftcdn.net/jpg/500_FemVonDcttCeKiOXFk.jpg', 'path');
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
                'delete' => false
            ],
            [
                'document' => $this->getDocument('filepath.jpg'),
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
        $pathAttribute = $this->getMockForAbstractClass(AttributeInterface::class);
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
