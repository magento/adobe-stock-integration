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
use Magento\AdobeStockImage\Model\SaveImage;
use Magento\AdobeStockImage\Model\SaveImageFile;
use Magento\AdobeStockImage\Model\SaveKeywords;
use Magento\AdobeStockImage\Model\SaveMediaGalleryAsset;
use Magento\Framework\Api\Search\Document;
use Magento\Framework\Exception\CouldNotSaveException;
use Magento\Framework\TestFramework\Unit\Helper\ObjectManager;
use Magento\MediaGalleryApi\Api\Data\AssetInterface;
use Magento\MediaGalleryApi\Api\GetAssetsByPathsInterface;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

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
     * @var MockObject|SaveKeywords
     */
    private $saveKeywords;

    /**
     * @var SaveImageFile|MockObject
     */
    private $saveImageFile;

    /**
     * @var SaveMediaGalleryAsset|MockObject
     */
    private $saveMediaGalleryAsset;

    /**
     * @var GetAssetsByPathsInterface|MockObject
     */
    private $getMediaGalleryAssetByPath;

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
        $this->saveKeywords = $this->createMock(SaveKeywords::class);
        $this->saveImageFile = $this->createMock(SaveImageFile::class);
        $this->saveMediaGalleryAsset = $this->createMock(SaveMediaGalleryAsset::class);
        $this->getMediaGalleryAssetByPath = $this->createMock(GetAssetsByPathsInterface::class);
        $this->saveImage = (new ObjectManager($this))->getObject(
            SaveImage::class,
            [
                'saveAdobeStockAsset' =>  $this->saveAdobeStockAsset,
                'documentToAsset' =>  $this->documentToAsset,
                'saveAssetKeywords' => $this->saveKeywords,
                'documentToKeywords' => $this->documentToKeywords,
                'saveImageFile' => $this->saveImageFile,
                'saveMediaGalleryAsset' => $this->saveMediaGalleryAsset,
                'getMediaGalleryAssetByPath' => $this->getMediaGalleryAssetByPath
            ]
        );
    }

    /**
     * Verify that image from the Adobe Stock can be saved.
     *
     * @throws CouldNotSaveException
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function testExecute(): void
    {
        $document = $this->createMock(Document::class);
        $url = 'https://as2.ftcdn.net/jpg/500_FemVonDcttCeKiOXFk.jpg';
        $destinationPath = 'path';
        $keywords = [];
        $assetId = 42;
        $mediaAsset = $this->createMock(AssetInterface::class);
        $mediaAsset->expects($this->once())
            ->method('getId')
            ->willReturn($assetId);

        $this->saveImageFile->expects($this->once())
            ->method('execute')
            ->with($document, $url, $destinationPath);

        $this->saveMediaGalleryAsset->expects($this->once())
            ->method('execute')
            ->with($document, $destinationPath);

        $this->getMediaGalleryAssetByPath->expects($this->once())
            ->method('execute')
            ->with([$destinationPath])
            ->willReturn([$mediaAsset]);

        $this->documentToKeywords->expects($this->once())
            ->method('convert')
            ->with($document)
            ->willReturn($keywords);

        $this->saveKeywords->expects($this->once())
            ->method('execute')
            ->with($assetId, $keywords);

        $this->documentToAsset->expects($this->once())
            ->method('convert')
            ->with($document, ['media_gallery_id' => $assetId]);

        $this->saveAdobeStockAsset->expects($this->once())
            ->method('execute');

        $this->saveImage->execute($document, $url, $destinationPath);
    }
}
