<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Magento\AdobeStockImage\Test\Unit\Model;

use Magento\AdobeStockImage\Model\Extract\MediaGalleryAsset as DocumentToAsset;
use Magento\AdobeStockImage\Model\SaveKeywords;
use Magento\AdobeStockImage\Model\SaveMediaGalleryAsset;
use Magento\Framework\Api\Search\Document;
use Magento\Framework\Exception\CouldNotSaveException;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Filesystem\Directory\Read;
use Magento\Framework\TestFramework\Unit\Helper\ObjectManager;
use Magento\MediaGalleryApi\Api\Data\AssetInterface;
use Magento\MediaGalleryApi\Api\GetAssetsByPathsInterface;
use Magento\MediaGalleryApi\Api\SaveAssetsInterface;
use Magento\MediaGallerySynchronizationApi\Api\SynchronizeFilesInterface;
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
     * @var DocumentToAsset|MockObject
     */
    private $documentToAsset;

    /**
     * @var Read|MockObject
     */
    private $mediaDirectory;

    /**
     * @var SaveMediaGalleryAsset
     */
    private $saveMediaAsset;

    /**
     * @var SaveKeywords|MockObject
     */
    private $saveKeywords;

    /**
     * @var GetAssetsByPathsInterface|MockObject
     */
    private $getAssetsByPaths;

    /**
     * @var SynchronizeFilesInterface|MockObject
     */
    private $importFiles;

    /**
     * @inheritdoc
     */
    protected function setUp(): void
    {
        $this->saveAssets = $this->createMock(SaveAssetsInterface::class);
        $this->saveKeywords = $this->createMock(SaveKeywords::class);
        $this->documentToAsset = $this->createMock(DocumentToAsset::class);
        $this->mediaDirectory = $this->createMock(Read::class);
        $this->getAssetsByPaths = $this->createMock(GetAssetsByPathsInterface::class);
        $this->importFiles = $this->createMock(SynchronizeFilesInterface::class);

        $this->saveMediaAsset = (new ObjectManager($this))->getObject(
            SaveMediaGalleryAsset::class,
            [
                'saveAssets' =>  $this->saveAssets,
                'saveKeywords' =>  $this->saveKeywords,
                'documentToAsset' =>  $this->documentToAsset,
                'getAssetsByPaths' => $this->getAssetsByPaths,
                'importFiles' => $this->importFiles
            ]
        );
    }

    /**
     * Verify successful save of a media gallery asset id.
     *
     * @dataProvider imageDataProvider
     * @param Document $document
     * @param string $path
     * @throws CouldNotSaveException
     * @throws LocalizedException
     */
    public function testExecute(Document $document, string $path): void
    {
        $asset = $this->createMock(AssetInterface::class);
        $assetId = 42;

        $this->documentToAsset->expects($this->once())
            ->method('convert')
            ->willReturn($asset);

        $this->saveAssets->expects($this->once())
            ->method('execute')
            ->with([$asset]);

        $this->getAssetsByPaths->expects($this->once())
            ->method('execute')
            ->with([$path])
            ->willReturn([$asset]);

        $asset->expects($this->any())
            ->method('getId')
            ->willReturn($assetId);

        $this->saveKeywords->expects($this->once())
            ->method('execute')
            ->with($assetId, $document);

        $this->importFiles->expects($this->once())
            ->method('execute')
            ->with([$path]);

        $this->assertEquals(
            $assetId,
            $this->saveMediaAsset->execute($document, $path)
        );
    }

    /**
     * Data provider for testExecute
     *
     * @return array[]
     */
    public function imageDataProvider(): array
    {
        return [
            [
                $this->createMock(Document::class),
                'catalog/test-image.jpeg'
            ]
        ];
    }
}
