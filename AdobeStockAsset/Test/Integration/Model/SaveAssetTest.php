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
use Magento\AdobeStockAssetApi\Api\SaveAssetInterface;
use Magento\MediaGalleryApi\Api\GetAssetsByPathsInterface;
use Magento\MediaGalleryApi\Api\SaveAssetsInterface;
use Magento\TestFramework\Helper\Bootstrap;
use PHPUnit\Framework\TestCase;

/**
 * Provides integration tests for saving and Adobe Stock asset.
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class SaveAssetTest extends TestCase
{
    /**
     * @var AssetRepositoryInterface
     */
    private $assetRepository;

    /**
     * @var SaveAssetsInterface
     */
    private $saveAsset;

    /**
     * @inheritdoc
     */
    protected function setUp(): void
    {
        $this->assetRepository = Bootstrap::getObjectManager()->get(AssetRepositoryInterface::class);
        $this->saveAsset = Bootstrap::getObjectManager()->get(SaveAssetInterface::class);
    }

    /**
     * Test save an Adobe Stock asset.
     * @magentoDataFixture ../../../../app/code/Magento/AdobeStockAsset/Test/_files/media_asset.php
     * @magentoDataFixture ../../../../app/code/Magento/AdobeStockAsset/Test/_files/category.php
     * @magentoDataFixture ../../../../app/code/Magento/AdobeStockAsset/Test/_files/creator.php
     */
    public function testExecute(): void
    {
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
        /** @var GetAssetsByPathsInterface $mediaGetByPath */
        $mediaGetByPath = Bootstrap::getObjectManager()->get(GetAssetsByPathsInterface::class);
        $mediaAssetId = $mediaGetByPath->execute(['some/path.jpg'])[0]->getId();

        /** @var CategoryRepositoryInterface $categoryRepository */
        $categoryRepository = Bootstrap::getObjectManager()->get(CategoryRepositoryInterface::class);
        $category= $categoryRepository->getById(42);

        /** @var CreatorRepositoryInterface $creatorRepository */
        $creatorRepository = Bootstrap::getObjectManager()->get(CreatorRepositoryInterface::class);
        $creator = $creatorRepository->getById(42);

        /** @var AssetInterfaceFactory $assetFactory */
        $assetFactory = Bootstrap::getObjectManager()->get(AssetInterfaceFactory::class);
        /** @var AssetInterface $asset */
        $asset = $assetFactory->create(
            [
                'data' => [
                    'id' => 1,
                    'is_licensed' => 1,
                    'media_gallery_id' => $mediaAssetId,
                    'category' => $category,
                    'creator' => $creator
                ]
            ]
        );

        return $asset;
    }

    /**
     * Clean up test entries.
     *
     * @param AssetInterface $asset
     * @throws \Exception
     */
    private function cleanUpEntries(AssetInterface $asset): void
    {
        $this->assetRepository->deleteById($asset->getId());
    }
}
