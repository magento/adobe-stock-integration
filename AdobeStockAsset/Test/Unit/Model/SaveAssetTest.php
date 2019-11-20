<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Magento\AdobeStockAsset\Test\Unit\Model;

use Magento\MediaGalleryApi\Model\DataExtractorInterface;
use Magento\AdobeStockAsset\Model\SaveAsset;
use Magento\AdobeStockAssetApi\Api\Data\AssetInterface;
use Magento\AdobeStockAssetApi\Api\Data\AssetInterfaceFactory;
use Magento\AdobeStockAssetApi\Api\Data\CategoryInterface;
use Magento\AdobeStockAssetApi\Api\Data\CreatorInterface;
use Magento\Framework\TestFramework\Unit\Helper\ObjectManager;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Magento\AdobeStockAssetApi\Api\AssetRepositoryInterface;
use Magento\AdobeStockAssetApi\Api\CreatorRepositoryInterface;
use Magento\AdobeStockAssetApi\Api\CategoryRepositoryInterface;

/**
 * Test for save asset service.
 */
class SaveAssetTest extends TestCase
{
    /**
     * @var MockObject|AssetRepositoryInterface
     */
    private $assetRepository;

    /**
     * @var MockObject|AssetInterfaceFactory
     */
    private $assetFactory;

    /**
     * @var MockObject|CreatorRepositoryInterface
     */
    private $creatorRepository;

    /**
     * @var MockObject|CategoryRepositoryInterface
     */
    private $categoryRepository;

    /**
     * @var MockObject|DataExtractorInterface
     */
    private $dataExtractor;

    /**
     * @var SaveAsset
     */
    private $saveAsset;

    /**
     * @inheritdoc
     */
    public function setUp(): void
    {
        $this->assetFactory = $this->createMock(AssetInterfaceFactory::class);
        $this->assetRepository = $this->createMock(AssetRepositoryInterface::class);
        $this->creatorRepository = $this->createMock(CreatorRepositoryInterface::class);
        $this->categoryRepository = $this->createMock(CategoryRepositoryInterface::class);
        $this->dataExtractor = $this->createMock(DataExtractorInterface::class);

        $this->saveAsset = (new ObjectManager($this))->getObject(
            SaveAsset::class,
            [
                'assetFactory' => $this->assetFactory,
                'assetRepository' => $this->assetRepository,
                'creatorRepository' => $this->creatorRepository,
                'categoryRepository' => $this->categoryRepository,
                'dataExtractor' => $this->dataExtractor
            ]
        );
    }

    public function testExecute(): void
    {
        $data = [
            'id' => 1,
            'media_gallery_id' => 2
        ];
        $categoryId = 5;
        $creatorId = 6;
        $finalData = [
            'id' => 1,
            'media_gallery_id' => 2,
            'category_id' => $categoryId,
            'creator_id' => $creatorId
        ];

        $asset = $this->createMock(AssetInterface::class);

        $this->dataExtractor->expects($this->once())
            ->method('extract')
            ->with($asset, AssetInterface::class)
            ->willReturn($data);

        $categoryMock = $this->createMock(CategoryInterface::class);
        $categoryMock->expects($this->once())->method('getId')
            ->willReturn($categoryId);
        $asset->expects($this->once())
            ->method('getCategory')
            ->willReturn($categoryMock);
        $this->categoryRepository->expects($this->once())->method('save')
            ->with($categoryMock)
            ->willReturn($categoryMock);

        $creatorInterface = $this->createMock(CreatorInterface::class);
        $creatorInterface->expects($this->once())
            ->method('getId')
            ->willReturn($creatorId);
        $asset->expects($this->once())
            ->method('getCreator')
            ->willReturn($creatorInterface);
        $this->creatorRepository->expects($this->once())
            ->method('save')
            ->with($creatorInterface)
            ->willReturn($creatorInterface);

        $finalAsset = $this->createMock(AssetInterface::class);

        $this->assetFactory->expects($this->once())
            ->method('create')
            ->with(['data' => $finalData])
            ->willReturn($finalAsset);

        $this->assetRepository->expects($this->once())->method('save')
            ->with($finalAsset);

        $this->saveAsset->execute($asset);
    }

    public function assetDataProvider()
    {
        return [
            [
                [
                    'id' => 1,
                    'media_gallery_id' => 2,
                    'creator'
                ]
            ]
        ];
    }
}
