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
use Magento\MediaGalleryApi\Api\SaveAssetsInterface;
use Magento\MediaGallerySynchronizationApi\Model\GetContentHashInterface;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Magento\MediaGalleryMetadataApi\Api\ExtractMetadataInterface;
use Magento\Framework\Api\AttributeValueFactory;
use Magento\Framework\Api\AttributeValue;
use Magento\MediaGalleryMetadataApi\Api\Data\MetadataInterface;

/**
 * Test saving a media gallery asset and return its id.
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class SaveMediaGalleryAssetTest extends TestCase
{
    /**
     * @var SaveAssetsInterface|MockObject
     */
    private $saveAssets;

    /**
     * @var DocumentToMediaGalleryAsset|MockObject
     */
    private $converter;

    /**
     * @var FileSystem|MockObject
     */
    private $filesystem;

    /**
     * @var Read|MockObject
     */
    private $mediaDirectory;

    /**
     * @var SaveMediaGalleryAsset
     */
    private $saveMediaAsset;

    /**
     * @var GetContentHashInterface|MockObject
     */
    private $getContentHash;

    /**
     * @var ExtractMetadataInterface|MockObject
     */
    private $extractMetadata;

    /**
     * @var AttributeValueFactory|MockObject
     */
    private $attributeValueFactory;

    /**
     * @inheritdoc
     */
    protected function setUp(): void
    {
        $this->saveAssets = $this->createMock(SaveAssetsInterface::class);
        $this->getContentHash = $this->createMock(GetContentHashInterface::class);
        $this->converter = $this->createMock(DocumentToMediaGalleryAsset::class);
        $this->filesystem = $this->createMock(Filesystem::class);
        $this->mediaDirectory = $this->createMock(Read::class);
        $this->extractMetadata = $this->createMock(ExtractMetadataInterface::class);
        $this->attributeValueFactory = $this->createMock(AttributeValueFactory::class);

        $this->saveMediaAsset = (new ObjectManager($this))->getObject(
            SaveMediaGalleryAsset::class,
            [
                'saveMediaAsset' =>  $this->saveAssets,
                'documentToMediaGalleryAsset' =>  $this->converter,
                'fileSystem' => $this->filesystem,
                'extractMetadata' => $this->extractMetadata,
                'attributeValueFactory' => $this->attributeValueFactory
            ]
        );
        $reflection = new \ReflectionClass(get_class($this->saveMediaAsset));
        $reflectionMethod = $reflection->getMethod('calculateFileSize');
        $reflectionMethod->setAccessible(true);
    }

    /**
     * Verify successful save of a media gallery asset id.
     *
     * @throws CouldNotSaveException
     */
    public function testExecute(): void
    {
        $document = $this->createMock(Document::class);
        $destinationPath = 'path';

        $this->filesystem->expects($this->atLeastOnce())
            ->method('getDirectoryRead')
            ->with(DirectoryList::MEDIA)
            ->willReturn($this->mediaDirectory);

        $this->mediaDirectory->expects($this->atLeastOnce())
            ->method('getAbsolutePath')
            ->with($destinationPath)
            ->willReturn('root/pub/media/catalog/test-image.jpeg');

        $fileSize = 42;
        $this->mediaDirectory->expects($this->once())
            ->method('stat')
            ->willReturn(['size' => $fileSize]);

        $hash = 'hash';

        $this->mediaDirectory->expects($this->once())
            ->method('readFile')
            ->willReturn($hash);

        $additionalData = [
            'id' => null,
            'path' => $destinationPath,
            'source' => 'Adobe Stock',
            'size' => $fileSize,
            'hash' => $this->getContentHash->execute($hash)
        ];
        $attributeMock = $this->createMock(AttributeValue::class);
        $metadataMock = $this->createMock(MetadataInterface::class);
        $document->expects($this->once())
             ->method('getCustomAttributes')
             ->willReturn([]);
        $this->extractMetadata->expects($this->once())
             ->method('execute')
             ->willReturn($metadataMock);
        $this->attributeValueFactory->expects($this->once())
             ->method('create')
             ->willReturn($attributeMock);
        
        $mediaGalleryAssetMock = $this->createMock(Asset::class);
        $this->converter->expects($this->once())
            ->method('convert')
            ->with($document, $additionalData)
            ->willReturn($mediaGalleryAssetMock);
        $attributeMock->expects($this->once())
            ->method('setAttributeCode');
        $attributeMock->expects($this->once())
            ->method('setValue');
        $metadataMock->expects($this->once())
            ->method('getDescription');
        $document->expects($this->once())
            ->method('setCustomAttributes');

        $this->saveAssets->expects($this->once())
            ->method('execute')
            ->with([$mediaGalleryAssetMock]);

        $this->saveMediaAsset->execute($document, $destinationPath);
    }
}
