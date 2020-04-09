<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Magento\AdobeStockImage\Test\Unit\Model;

use Magento\AdobeStockAssetApi\Api\AssetRepositoryInterface;
use Magento\AdobeStockAssetApi\Api\SaveAssetInterface;
use Magento\AdobeStockImage\Model\Extract\AdobeStockAsset as DocumentToAsset;
use Magento\AdobeStockImage\Model\Extract\Keywords as DocumentToKeywords;
use Magento\AdobeStockImage\Model\SaveImage;
use Magento\AdobeStockImage\Model\SaveImageFile;
use Magento\AdobeStockImage\Model\SaveMediaGalleryAsset;
use Magento\Framework\Api\Search\Document;
use Magento\Framework\Exception\CouldNotSaveException;
use Magento\Framework\TestFramework\Unit\Helper\ObjectManager;
use Magento\MediaGalleryApi\Api\Data\AssetInterface;
use Magento\MediaGalleryApi\Model\Asset\Command\GetByPathInterface;
use Magento\MediaGalleryApi\Model\Keyword\Command\SaveAssetKeywordsInterface;
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
     * @var MockObject|SaveAssetInterface
     */
    private $saveAdobeStockAsset;

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
     * @var SaveImageFile|MockObject
     */
    private $saveImageFileMock;

    /**
     * @var SaveMediaGalleryAsset|MockObject
     */
    private $saveMediaGalleryAssetMock;

    /**
     * @var GetByPathInterface|MockObject
     */
    private $getMediaGalleryAssetByPathMock;

    /**
     * @var AssetRepositoryInterface|MockObject
     */
    private $assetRepositoryMock;

    /**
     * @var LoggerInterface|MockObject
     */
    private $loggerMock;

    /**
     * @var SaveImage
     */
    private $saveImage;

    /**
     * @inheritdoc
     */
    protected function setUp(): void
    {
        $this->saveAdobeStockAsset = $this->createMock(SaveAssetInterface::class);
        $this->documentToAsset = $this->createMock(DocumentToAsset::class);
        $this->documentToKeywords = $this->createMock(DocumentToKeywords::class);
        $this->saveAssetKeywords = $this->createMock(SaveAssetKeywordsInterface::class);
        $this->saveImageFileMock = $this->createMock(SaveImageFile::class);
        $this->saveMediaGalleryAssetMock = $this->createMock(SaveMediaGalleryAsset::class);
        $this->getMediaGalleryAssetByPathMock = $this->createMock(GetByPathInterface::class);
        $this->assetRepositoryMock = $this->createMock(AssetRepositoryInterface::class);
        $this->loggerMock = $this->createMock(LoggerInterface::class);

        $this->saveImage = (new ObjectManager($this))->getObject(
            SaveImage::class,
            [
                'saveAdobeStockAsset' =>  $this->saveAdobeStockAsset,
                'documentToAsset' =>  $this->documentToAsset,
                'saveAssetKeywords' => $this->saveAssetKeywords,
                'documentToKeywords' => $this->documentToKeywords,
                'saveImageFile' => $this->saveImageFileMock,
                'saveMediaGalleryAsset' => $this->saveMediaGalleryAssetMock,
                'getMediaGalleryAssetByPath' => $this->getMediaGalleryAssetByPathMock,
                'assetRepository' => $this->assetRepositoryMock,
                'logger' => $this->loggerMock
            ]
        );
    }

    /**
     * Verify that image from the Adobe Stock can be saved.
     *
     * @param Document $document
     * @param string $url
     * @param string $destinationPath
     * @param AssetInterface $mediaAsset
     * @param bool $assetAlreadyExists
     *
     * @throws CouldNotSaveException
     * @dataProvider assetProvider
     */
    public function testExecute(
        Document $document,
        string $url,
        string $destinationPath,
        AssetInterface $mediaAsset,
        bool $assetAlreadyExists
    ): void {
        $mediaGalleryAssetId = 42;
        $keywords = [];

        $this->saveImageFileMock->expects($this->once())
            ->method('execute')
            ->with($document, $url, $destinationPath);

        $this->saveMediaGalleryAssetMock->expects($this->once())
            ->method('execute')
            ->with($document, $destinationPath);

        $this->getMediaGalleryAssetByPathMock->expects($this->once())
            ->method('execute')
            ->with($destinationPath)
            ->willReturn($mediaAsset);

        if (!$assetAlreadyExists) {
            $adobeStockAssetMock = $this->createMock(\Magento\AdobeStockAssetApi\Api\Data\AssetInterface::class);
            $this->assetRepositoryMock->expects($this->once())
                ->method('getById')
                ->with($document->getId())
                ->willReturn($adobeStockAssetMock);
            $adobeStockAssetMock->expects($this->once())
                ->method('getMediaGalleryId')
                ->willReturn($mediaGalleryAssetId);
        }

        $this->documentToKeywords->expects($this->once())
            ->method('convert')
            ->with($document)
            ->willReturn($keywords);

        $this->saveAssetKeywords->expects($this->once())
            ->method('execute')
            ->with($keywords, $mediaGalleryAssetId);

        $this->documentToAsset->expects($this->once())
            ->method('convert')
            ->with($document, ['media_gallery_id' => $mediaGalleryAssetId]);

        $this->saveAdobeStockAsset->expects($this->once())
            ->method('execute');

        $this->saveImage->execute($document, $url, $destinationPath);
    }

    /**
     * Test save image with exception.
     *
     * @param Document $document
     * @param string $url
     * @param string $destinationPath
     *
     * @throws CouldNotSaveException
     * @dataProvider assetProviderForExceptionTest
     */
    public function testSaveImageWithException(
        Document $document,
        string $url,
        string $destinationPath
    ): void {
        $this->saveImageFileMock->expects($this->once())
            ->method('execute')
            ->with($document, $url, $destinationPath)
            ->willThrowException(new \Exception('Some Exception'));

        $this->expectException(CouldNotSaveException::class);

        $this->loggerMock->expects($this->once())
            ->method('critical')
            ->willReturnSelf();

        $this->saveImage->execute($document, $url, $destinationPath);
    }

    /**
     * Asset provider for test with exception
     *
     * @return array
     */
    public function assetProviderForExceptionTest(): array
    {
        return [
            [
                'document' => $this->getDocumentMock(),
                'url' => 'https://as2.ftcdn.net/jpg/500_FemVonDcttCeKiOXFk.jpg',
                'destinationPath' => 'path'
            ]
        ];
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
                'document' => $this->getDocumentMock(12345),
                'url' => 'https://as2.ftcdn.net/jpg/500_FemVonDcttCeKiOXFk.jpg',
                'destinationPath' => 'path',
                'mediaAsset' => $this->getMediaAssetMock(0),
                'assetAlreadyExists' => false
            ],
            [
                'document' => $this->getDocumentMock(),
                'url' => 'https://as2.ftcdn.net/jpg/500_FemVonDcttCeKiOXFk.jpg',
                'destinationPath' => 'path',
                'mediaAsset' => $this->getMediaAssetMock(42),
                'assetAlreadyExists' => true
            ]
        ];
    }

    /**
     * Get media gallery asset mock object.
     *
     * @param int|null $assetId
     *
     * @return MockObject
     */
    private function getMediaAssetMock(int $assetId = null): MockObject
    {
        $mediaAssetMock = $this->createMock(AssetInterface::class);
        $mediaAssetMock->expects($this->once())
            ->method('getId')
            ->willReturn($assetId);

        return $mediaAssetMock;
    }

    /**
     * Get document mock object.
     *
     * @param int|null $assetId
     *
     * @return MockObject
     */
    private function getDocumentMock(int $assetId = null): MockObject
    {
        $document = $this->createMock(Document::class);
        if ($assetId) {
            $document->method('getId')
                ->willReturn($assetId);
        }

        return $document;
    }
}
