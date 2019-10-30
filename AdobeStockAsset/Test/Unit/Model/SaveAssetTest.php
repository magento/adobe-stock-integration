<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magento\AdobeStockAsset\Test\Unit\Model;

use Magento\AdobeStockAsset\Model\SaveAsset;
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
     * @var MockObject|CreatorRepositoryInterface
     */
    private $creatorRepository;

    /**
     * @var MockObject|CategoryRepositoryInterface
     */
    private $categoryRepository;

    /**
     * @var SaveAsset
     */
    private $saveAsset;

    /**
     * @inheritdoc
     */
    public function setUp()
    {
        $this->assetRepository = $this->createMock(AssetRepositoryInterface::class);
        $this->creatorRepository = $this->createMock(CreatorRepositoryInterface::class);
        $this->categoryRepository = $this->createMock(CategoryRepositoryInterface::class);

        $this->saveAsset = new SaveAsset(
            $this->assetRepository,
            $this->creatorRepository,
            $this->categoryRepository
        );
    }

    public function testExecute()
    {
        $categoryId = 5;
        $creatorId = 5;

        $asset = $this->createMock(\Magento\AdobeStockAssetApi\Api\Data\AssetInterface::class);
        $categoryMock = $this->createMock(\Magento\AdobeStockAssetApi\Api\Data\CategoryInterface::class);
        $categoryMock->expects($this->once())->method('getId')
            ->willReturn($categoryId);
        $asset->expects($this->once())
            ->method('getCategory')
            ->willReturn($categoryMock);
        $asset->expects($this->once())
            ->method('setCategoryId')
            ->with($categoryId);
        $creatorInterface = $this->createMock(\Magento\AdobeStockAssetApi\Api\Data\CreatorInterface::class);
        $this->categoryRepository->expects($this->once())->method('save')
            ->with($categoryMock)
            ->willReturn($categoryMock);
        $asset->expects($this->once())->method('getCreator')->willReturn($creatorInterface);
        $creatorInterface->expects($this->once())
            ->method('getId')
            ->willReturn($creatorId);
        $this->creatorRepository->expects($this->once())
            ->method('save')
            ->with($creatorInterface)
            ->willReturn($creatorInterface);
        $asset->expects($this->once())
            ->method('setCreatorId')
            ->with($creatorId);
        $this->assetRepository->expects($this->once())->method('save')
            ->with($asset);
        $this->saveAsset->execute($asset);
    }
}
