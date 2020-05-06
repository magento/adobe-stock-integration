<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

declare(strict_types=1);

namespace Magento\AdobeStockAsset\Test\Integration\Model;

use Magento\AdobeStockAssetApi\Api\AssetRepositoryInterface;
use Magento\AdobeStockAssetApi\Api\CategoryRepositoryInterface;
use Magento\AdobeStockAssetApi\Api\CreatorRepositoryInterface;
use Magento\AdobeStockAssetApi\Api\Data\AssetInterface;
use Magento\AdobeStockAssetApi\Api\Data\AssetInterfaceFactory;
use Magento\AdobeStockAssetApi\Api\Data\CategoryInterface;
use Magento\AdobeStockAssetApi\Api\Data\CategoryInterfaceFactory;
use Magento\AdobeStockAssetApi\Api\Data\CreatorInterface;
use Magento\AdobeStockAssetApi\Api\Data\CreatorInterfaceFactory;
use Magento\AdobeStockAssetApi\Api\SaveAssetInterface;
use Magento\MediaGalleryApi\Api\Data\AssetInterface as MediaAsset;
use Magento\MediaGalleryApi\Api\Data\AssetInterfaceFactory as MediaAssetFactory;
use Magento\MediaGalleryApi\Api\DeleteAssetsByPathsInterface;
use Magento\MediaGalleryApi\Api\SaveAssetsInterface;
use Magento\TestFramework\Helper\Bootstrap;
use PHPUnit\Framework\TestCase;

/**
 * Provides integration tests for saving and Adobe Stock asset.
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class SaveAssetTest extends TestCase
{
    private const MEDIA_GALLERY_ASSET_PATH = 'some/path.jpg';

    /**
     * @var CategoryRepositoryInterface
     */
    private $categoryRepository;

    /**
     * @var CreatorRepositoryInterface
     */
    private $creatorRepository;

    /**
     * @var AssetRepositoryInterface
     */
    private $assetRepository;

    /**
     * @var DeleteAssetsByPathsInterface
     */
    private $deleteMediaAssetByPath;

    /**
     * @var SaveAssetsInterface
     */
    private $saveAsset;

    /**
     * @inheritdoc
     */
    public function setUp()
    {
        $this->categoryRepository = Bootstrap::getObjectManager()->get(CategoryRepositoryInterface::class);
        $this->creatorRepository = Bootstrap::getObjectManager()->get(CreatorRepositoryInterface::class);
        $this->assetRepository = Bootstrap::getObjectManager()->get(AssetRepositoryInterface::class);
        $this->deleteMediaAssetByPath = Bootstrap::getObjectManager()->get(DeleteAssetsByPathsInterface::class);
        $this->saveAsset = Bootstrap::getObjectManager()->get(SaveAssetInterface::class);
    }

    /**
     * Test save an Adobe Stock asset.
     */
    public function testExecute(): void
    {
        /** @var AssetInterface $asset */
        $asset = $this->prepareAsset();
        $this->saveAsset->execute($asset);
        $expectedAsset = $this->assetRepository->getById($asset->getId());

        $this->assertEquals($expectedAsset->getId(), $asset->getId());
        $this->assertEquals($expectedAsset->getCategoryId(), $asset->getCategory()->getId());
        $this->assertEquals($expectedAsset->getCreatorId(), $asset->getCreator()->getId());
        $this->assertEquals($expectedAsset->getMediaGalleryId(), $asset->getMediaGalleryId());

        $this->cleanUpEntries($expectedAsset);
    }

    /**
     * Prepare an Adobe Stock asset test object.
     */
    public function prepareAsset(): AssetInterface
    {
        /** @var AssetInterfaceFactory $assetFactory */
        $assetFactory = Bootstrap::getObjectManager()->get(AssetInterfaceFactory::class);
        /** @var AssetInterface $asset */
        $asset = $assetFactory->create(
            [
                'data' => [
                    'id' => 1,
                    'is_licensed' => 1,
                    'media_gallery_id' => $mediaId,
                    'category' => $category,
                    'creator' => $creator
                ]
            ]
        );

        return $asset;
    }

    /**
     * Clean up test entries.
     */
    private function cleanUpEntries(AssetInterface $asset): void
    {
        $this->assetRepository->deleteById($asset->getId());
    }
}
