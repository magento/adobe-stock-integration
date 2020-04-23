<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Magento\AdobeStockAsset\Test\Unit\Model;

use Magento\AdobeStockAsset\Model\SaveAsset;
use Magento\AdobeStockAssetApi\Api\AssetRepositoryInterface;
use Magento\AdobeStockAssetApi\Api\CategoryRepositoryInterface;
use Magento\AdobeStockAssetApi\Api\CreatorRepositoryInterface;
use Magento\AdobeStockAssetApi\Api\Data\AssetInterface;
use Magento\AdobeStockAssetApi\Api\Data\AssetInterfaceFactory;
use Magento\AdobeStockAssetApi\Api\Data\CategoryInterface;
use Magento\AdobeStockAssetApi\Api\Data\CreatorInterface;
use Magento\Framework\Reflection\DataObjectProcessor;
use Magento\Framework\TestFramework\Unit\Helper\ObjectManager;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

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
     * @var MockObject|DataObjectProcessor
     */
    private $objectProcessor;

    /**
     * @var SaveAsset
     */
    private $saveAsset;

    /**
     * @inheritdoc
     */
    protected function setUp(): void
    {
        $this->assetFactory = $this->createMock(AssetInterfaceFactory::class);
        $this->assetRepository = $this->createMock(AssetRepositoryInterface::class);
        $this->creatorRepository = $this->createMock(CreatorRepositoryInterface::class);
        $this->categoryRepository = $this->createMock(CategoryRepositoryInterface::class);
        $this->objectProcessor = $this->createMock(DataObjectProcessor::class);

        $this->saveAsset = (new ObjectManager($this))->getObject(
            SaveAsset::class,
            [
                'assetFactory' => $this->assetFactory,
                'assetRepository' => $this->assetRepository,
                'creatorRepository' => $this->creatorRepository,
                'categoryRepository' => $this->categoryRepository,
                'objectProcessor' => $this->objectProcessor
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

        $asset = $this->createMock(AssetInterface::class);

        $this->objectProcessor->expects($this->once())
            ->method('buildOutputDataArray')
            ->with($asset, AssetInterface::class)
            ->willReturn($data);

        $category = $this->createMock(CategoryInterface::class);
        $category->expects($this->once())->method('getId')
            ->willReturn($categoryId);
        $asset->expects($this->once())
            ->method('getCategory')
            ->willReturn($category);
        $this->categoryRepository->expects($this->once())->method('save')
            ->with($category)
            ->willReturn($category);

        $creator = $this->createMock(CreatorInterface::class);
        $creator->expects($this->once())
            ->method('getId')
            ->willReturn($creatorId);
        $asset->expects($this->once())
            ->method('getCreator')
            ->willReturn($creator);
        $this->creatorRepository->expects($this->once())
            ->method('save')
            ->with($creator)
            ->willReturn($creator);

        $finalData = [
            'id' => 1,
            'media_gallery_id' => 2,
            'category_id' => $categoryId,
            'creator_id' => $creatorId,
            'category' => $category,
            'creator' => $creator
        ];

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
