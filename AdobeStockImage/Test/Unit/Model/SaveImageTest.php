<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magento\AdobeStockImage\Test\Unit\Model;

use Magento\AdobeMediaGalleryApi\Model\Asset\Command\SaveInterface;
use Magento\AdobeMediaGalleryApi\Model\Keyword\Command\GetAssetKeywordsInterface;
use Magento\AdobeStockAsset\Model\DocumentToAsset;
use Magento\AdobeStockAsset\Model\DocumentToMediaGalleryAsset;
use Magento\AdobeStockAssetApi\Api\SaveAssetInterface;
use Magento\Framework\Api\AttributeInterface;
use Magento\Framework\Api\Search\DocumentInterface;
use Magento\Framework\TestFramework\Unit\Helper\ObjectManager;
use Magento\AdobeStockImage\Model\SaveImage;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Magento\AdobeStockImage\Model\Storage;
use Psr\Log\LoggerInterface;

/**
 * Test for Save image model.
 */
class SaveImageTest extends TestCase
{
    /**
     * @var MockObject|Storage
     */
    private $storage;

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
     * @var MockObject|GetAssetKeywordsInterface
     */
    private $getAssetKeywords;

    /**
     * @var SaveImage
     */
    private $saveImage;

    /**
     * @inheritDoc
     */
    public function setUp()
    {
        $this->storage = $this->createMock(Storage::class);
        $this->logger = $this->createMock(LoggerInterface::class);
        $this->saveMediaAsset = $this->createMock(SaveInterface::class);
        $this->saveAdobeStockAsset = $this->createMock(SaveAssetInterface::class);
        $this->documentToMediaGalleryAsset = $this->createMock(DocumentToMediaGalleryAsset::class);
        $this->documentToAsset = $this->createMock(DocumentToAsset::class);
        $this->getAssetKeywords = $this->createMock(GetAssetKeywordsInterface::class);

        $this->saveImage = (new ObjectManager($this))->getObject(
            SaveImage::class,
            [
                'storage' => $this->storage,
                'logger' => $this->logger,
                'saveMediaAsset' =>  $this->saveMediaAsset,
                'saveAdobeStockAsset' =>  $this->saveAdobeStockAsset,
                'documentToMediaGalleryAsset' =>  $this->documentToMediaGalleryAsset,
                'documentToAsset' =>  $this->documentToAsset,
                'getAssetKeywords' => $this->getAssetKeywords
            ]
        );
    }

    /**
     * Verify that image can be saved.
     *
     * @param DocumentInterface $document
     * @param bool $delete
     * @throws \Magento\Framework\Exception\CouldNotSaveException
     * @dataProvider assetProvider
     */
    public function testExecute(DocumentInterface $document, bool $delete)
    {
        if ($delete) {
            $this->storage->expects($this->once())
                ->method('delete');
        } else {
            $this->storage->expects($this->never())
                ->method('delete');
        }

        $this->storage->expects($this->once())
            ->method('save');

        $this->documentToMediaGalleryAsset->expects($this->once())
            ->method('convert')
            ->with($document);

        $mediaGalleryAssetId = 42;

        $this->saveMediaAsset->expects($this->once())
            ->method('execute')
            ->willReturn($mediaGalleryAssetId);

        $this->getAssetKeywords->expects($this->once())
            ->method('execute')
            ->with($mediaGalleryAssetId)
            ->willReturn([]);

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
     * @param string $path
     * @return DocumentInterface|MockObject
     */
    private function getDocument(string $path = null): DocumentInterface
    {
        $document = $this->createMock(DocumentInterface::class);
        $pathAttribute = $this->createMock(AttributeInterface::class);
        $pathAttribute->expects($this->any())
            ->method('getValue')
            ->willReturn($path);
        $document->expects($this->any())
            ->method('getCustomAttribute')
            ->with('path')
            ->willReturn($pathAttribute);

        return $document;
    }
}
