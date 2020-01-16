<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Magento\AdobeStockImage\Test\Unit\Model;

use Magento\AdobeStockAsset\Model\Asset;
use Magento\AdobeStockAssetApi\Api\AssetRepositoryInterface;
use Magento\AdobeStockAssetApi\Api\Data\AssetSearchResultsInterface;
use Magento\AdobeStockImage\Model\AssetIndexer;
use Magento\Framework\Api\SearchCriteriaBuilder;
use Magento\Framework\Api\SearchCriteriaInterface;
use Magento\Framework\App\ResourceConnection;
use Magento\Framework\DB\Adapter\AdapterInterface;
use Magento\Framework\Filesystem;
use Magento\Framework\Filesystem\Directory\ReadInterface;
use Magento\Framework\Filesystem\Driver\File;
use Magento\Framework\TestFramework\Unit\Helper\ObjectManager;
use Magento\MediaGalleryApi\Api\Data\AssetInterface;
use Magento\MediaGalleryApi\Model\Asset\Command\GetByPathInterface;
use PHPUnit\Framework\TestCase;
use PHPUnit_Framework_MockObject_MockObject as MockObject;

/**
 * Test for Magento\AdobeStockImage\Model\AssetIndexer
 */
class AssetIndexerTest extends TestCase
{
    /**
     * @var AssetIndexer
     */
    private $assetIndexer;

    /**
     * @var GetByPathInterface|MockObject
     */
    private $getByPathCommandMock;

    /**
     * @var ResourceConnection|MockObject
     */
    private $resourceConnectionMock;

    /**
     * @var AssetRepositoryInterface|MockObject
     */
    private $assetRepositoryMock;

    /**
     * @var SearchCriteriaBuilder|MockObject
     */
    private $searchCriteriaBuilderMock;

    /**
     * @var Filesystem|MockObject
     */
    private $fileSystemMock;

    /**
     * @var File|MockObject
     */
    private $fileMock;

    /**
     * @var Asset|MockObject
     */
    private $assetMock;

    /**
     * @var AdapterInterface|MockObject
     */
    private $adapderMock;

    /**
     * @inheritdoc
     */
    protected function setUp(): void
    {
        $objectManagerHelper = (new ObjectManager($this));

        $this->getByPathCommandMock = $this->createMock(GetByPathInterface::class);
        $this->resourceConnectionMock = $this->createMock(ResourceConnection::class);
        $this->assetRepositoryMock = $this->createMock(AssetRepositoryInterface::class);
        $this->searchCriteriaBuilderMock = $this->createMock(SearchCriteriaBuilder::class);
        $this->fileSystemMock = $this->createMock(Filesystem::class);
        $this->fileMock = $this->createMock(File::class);
        $this->assetMock = $this->createMock(Asset::class);
        $this->adapderMock = $this->createMock(AdapterInterface::class);

        $this->assetIndexer = $objectManagerHelper->getObject(
            AssetIndexer::class,
            [
                'getByPathCommand' => $this->getByPathCommandMock,
                'assetRepository' => $this->assetRepositoryMock,
                'searchCriteriaBuilder' => $this->searchCriteriaBuilderMock,
                'filesystem' => $this->fileSystemMock,
                'driver' => $this->fileMock,
                'resource' => $this->resourceConnectionMock
            ]
        );
    }

    /**
     * Verify that indexer set licensed data corectrly
     *
     * @dataProvider pathDataProvider
     * @param array $pathData
     * @param int $mediaAssetId
     * @return void
     */
    public function testExecute(array $pathData, int $mediaAssetId): void
    {
        $fileInfoMock = $this->createMock(\SplFileInfo::class);
        $mediaAssetMock = $this->createMock(AssetInterface::class);
        $directoryReadMock = $this->createMock(ReadInterface::class);
        $searchCriteriaMock = $this->createMock(SearchCriteriaInterface::class);
        $searchResultMock = $this->createMock(AssetSearchResultsInterface::class);

        $this->fileSystemMock->expects($this->once())
            ->method('getDirectoryRead')
            ->willReturn($directoryReadMock);
        $directoryReadMock->expects($this->once())
            ->method('getRelativePath')
            ->willReturn($pathData['fileName']);
        $this->fileMock->expects($this->once())
            ->method('getParentDirectory')
            ->willReturn(($pathData['is_slash']) ? '/' : '.');
        $fileInfoMock->expects($this->once())
            ->method('getPath')
            ->willReturn($pathData['path']);
        $fileInfoMock->expects($this->once())
            ->method('getFileName')
            ->willReturn($pathData['fileName']);
        $this->getByPathCommandMock->expects($this->once())
            ->method('execute')
            ->with($pathData['expected_path'])
            ->willReturn($mediaAssetMock);
        $this->searchCriteriaBuilderMock->expects($this->once())
            ->method('addFilter')
            ->with('media_gallery_id', $mediaAssetId)
            ->willReturn($searchCriteriaMock);
        $mediaAssetMock->expects($this->exactly(2))
            ->method('getId')
            ->willReturn(1);
        $this->searchCriteriaBuilderMock->expects($this->once())
            ->method('create')
            ->willReturn($searchCriteriaMock);
        $this->assetRepositoryMock->expects($this->once())
            ->method('getList')
            ->with($searchCriteriaMock)
            ->willReturn($searchResultMock);
        $searchResultMock->expects($this->once())
            ->method('getItems')
            ->willReturn([$this->assetMock]);
        $this->resourceConnectionMock->expects($this->once())
            ->method('getConnection')
            ->willReturn($this->adapderMock);
        $this->assetMock->expects($this->once())
            ->method('getIsLicensed')
            ->willReturn(1);
        $this->adapderMock->expects($this->once())
            ->method('insertOnDuplicate')
            ->willReturnSelf();
        $this->assetIndexer->execute($fileInfoMock);
    }

    /**
     * Path data provider for images indexer
     *
     * @return array
     */
    public function pathDataProvider(): array
    {
        return
            [
                [
                    [
                        'fileName' => 'theme/preview/image.png',
                        'path' => '/http/srv/pub/media/theme/preview/',
                        'expected_path' => 'theme/preview/image.png',
                        'is_slash' => true
                    ],
                    1
                ],
                [
                    [
                        'fileName' => 'image.png',
                        'path' => '/http/srv/pub/media/',
                        'expected_path' => '/image.png',
                        'is_slash' => false
                    ],
                    1
                ],
                [
                    [
                        'fileName' => 'new/folder/.theme/preview/image.png',
                        'path' => '/http/srv/pub/media/new/folder/.theme/preview/',
                        'expected_path' => 'new/folder/.theme/preview/image.png',
                        'is_slash' => true
                    ],
                    1
                ]
            ];
    }
}
