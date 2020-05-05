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
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

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
     * @inheritdoc
     */
    protected function setUp(): void
    {
        $this->saveAssets = $this->createMock(SaveAssetsInterface::class);
        $this->converter = $this->createMock(DocumentToMediaGalleryAsset::class);
        $this->filesystem = $this->createMock(Filesystem::class);
        $this->mediaDirectory = $this->createMock(Read::class);

        $this->saveMediaAsset = (new ObjectManager($this))->getObject(
            SaveMediaGalleryAsset::class,
            [
                'saveMediaAsset' =>  $this->saveAssets,
                'documentToMediaGalleryAsset' =>  $this->converter,
                'fileSystem' => $this->filesystem
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

        $this->filesystem->expects($this->once())
            ->method('getDirectoryRead')
            ->with(DirectoryList::MEDIA)
            ->willReturn($this->mediaDirectory);

        $this->mediaDirectory->expects($this->once())
            ->method('getAbsolutePath')
            ->with($destinationPath)
            ->willReturn('root/pub/media/catalog/test-image.jpeg');

        $fileSize = 42;
        $this->mediaDirectory->expects($this->once())
            ->method('stat')
            ->willReturn(['size' => $fileSize]);

        $additionalData = [
            'id' => null,
            'path' => $destinationPath,
            'source' => 'Adobe Stock',
            'size' => $fileSize,
        ];
        $mediaGalleryAssetMock = $this->createMock(Asset::class);
        $this->converter->expects($this->once())
            ->method('convert')
            ->with($document, $additionalData)
            ->willReturn($mediaGalleryAssetMock);

        $this->saveAssets->expects($this->once())
            ->method('execute')
            ->with([$mediaGalleryAssetMock]);

        $this->saveMediaAsset->execute($document, $destinationPath);
    }
}
