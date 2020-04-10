<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

declare(strict_types=1);

namespace Magento\AdobeStockAsset\Test\Integration;

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
use Magento\MediaGalleryApi\Model\Asset\Command\DeleteByPathInterface;
use Magento\MediaGalleryApi\Model\Asset\Command\SaveInterface;
use Magento\TestFramework\Helper\Bootstrap;
use PHPUnit\Framework\TestCase;

/**
 * Provides integration tests for saving and Adobe Stock asset.
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
     * @var DeleteByPathInterface
     */
    private $deleteMediaAssetByPath;

    /**
     * @var SaveAssetInterface
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
        $this->deleteMediaAssetByPath = Bootstrap::getObjectManager()->get(DeleteByPathInterface::class);
        $this->saveAsset = Bootstrap::getObjectManager()->get(SaveAssetInterface::class);
    }

    /**
     * Test save an Adobe Stock asset.
     *
     * @param AssetInterface $asset
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
     *
     * @return AssetInterface
     */
    public function prepareAsset(): AssetInterface
    {
        /** @var MediaAssetFactory $mediaAssetFactory */
        $mediaAssetFactory = Bootstrap::getObjectManager()->get(MediaAssetFactory::class);
        /** @var MediaAsset $mediaAsset */
        $mediaAsset = $mediaAssetFactory->create(
            [
                'data' => [
                    'path' => self::MEDIA_GALLERY_ASSET_PATH,
                    'title' => 'Web API test image',
                    'source' => 'Adobe Stock',
                    'content_type' => 'image/jpeg',
                    'width' => 6529,
                    'height' => 4355,
                    'size' => 424242
                ]
            ]
        );
        /** @var SaveInterface $mediaSave */
        $mediaSave = Bootstrap::getObjectManager()->get(SaveInterface::class);
        $mediaId = $mediaSave->execute($mediaAsset);

        $categoryFactory = Bootstrap::getObjectManager()->get(CategoryInterfaceFactory::class);
        /** @var CategoryInterface $category */
        $category = $categoryFactory->create(
            [
                'data' => [
                    'id' => 42,
                    'name' => 'Test asset category'
                ]
            ]
        );

        /** @var CreatorInterface $creatorFactory */
        $creatorFactory = Bootstrap::getObjectManager()->get(CreatorInterfaceFactory::class);
        /** @var CreatorInterface $creator */
        $creator = $creatorFactory->create(
            [
                'data' => [
                    'id' => 42,
                    'name' => 'Test asset creator'
                ]
            ]
        );

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
        $this->categoryRepository->deleteById($asset->getCategoryId());
        $this->creatorRepository->deleteById($asset->getCategoryId());
        $this->assetRepository->deleteById($asset->getId());
        $this->deleteMediaAssetByPath->execute(self::MEDIA_GALLERY_ASSET_PATH);
    }
}
