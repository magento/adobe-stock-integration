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
use Magento\AdobeStockAssetApi\Api\Data\CategoryInterfaceFactory;
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
     * @return array
     */
    public function getAssetData(): array
    {
        return [
            'asset_save' => [
                'data' => [
                    'media_gallery_path' => ['some/path.jpg'],
                    'category_id' => 42,
                    'creator_id' => 42,
                ]
            ],
            'without_category' => [
                'data' => [
                    'media_gallery_path' => ['some/path.jpg'],
                    'category_id' => null,
                    'creator_id' => 42,
                ]
            ]
        ];
    }

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
     *
     * @param array $caseData
     *
     * @dataProvider getAssetData
     * @magentoDataFixture ../../../../app/code/Magento/AdobeStockAsset/Test/_files/media_asset.php
     * @magentoDataFixture ../../../../app/code/Magento/AdobeStockAsset/Test/_files/category.php
     * @magentoDataFixture ../../../../app/code/Magento/AdobeStockAsset/Test/_files/creator.php
     */
    public function testExecute(array $caseData): void
    {
        $asset = $this->prepareAsset($caseData);
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
     * @param array $caseData
     * @return AssetInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function prepareAsset(array $caseData): AssetInterface
    {
        $assetData['data'] = [
            'id' => 1,
            'is_licensed' => 1,
        ];
        $assetData['data']['media_gallery_id'] = $this->getMediaAssetId($caseData['media_gallery_path']);
        $assetData['data']['category'] = $this->getCategory($caseData['category_id']);
        $assetData['data']['creator'] = $this->getCreator($caseData['creator_id']);
        /** @var AssetInterfaceFactory $assetFactory */
        $assetFactory = Bootstrap::getObjectManager()->get(AssetInterfaceFactory::class);
        /** @var AssetInterface $asset */
        $asset = $assetFactory->create($assetData);

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

    /**
     * @param $paths
     * @return int|null
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    protected function getMediaAssetId($paths): int
    {
        /** @var GetAssetsByPathsInterface $mediaGetByPath */
        $mediaGetByPath = Bootstrap::getObjectManager()->get(GetAssetsByPathsInterface::class);
        $mediaAssetId = $mediaGetByPath->execute($paths)[0]->getId();

        return $mediaAssetId;
    }

    /**
     * @param int|null $categoryId
     * @return \Magento\AdobeStockAssetApi\Api\Data\CategoryInterface|null
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    protected function getCategory(?int $categoryId): ?\Magento\AdobeStockAssetApi\Api\Data\CategoryInterface
    {
        /** @var CategoryRepositoryInterface $categoryRepository */
        $categoryRepository = Bootstrap::getObjectManager()->get(CategoryRepositoryInterface::class);
        /** @var CategoryInterfaceFactory $categoryRepository */
        $categoryFactory = Bootstrap::getObjectManager()->get(CategoryInterfaceFactory::class);

        return $categoryId !== null ? $categoryRepository->getById($categoryId) : $categoryFactory->create();
    }

    /**
     * @param int $creatorId
     * @return \Magento\AdobeStockAssetApi\Api\Data\CreatorInterface
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    protected function getCreator(int $creatorId): \Magento\AdobeStockAssetApi\Api\Data\CreatorInterface
    {
        /** @var CreatorRepositoryInterface $creatorRepository */
        $creatorRepository = Bootstrap::getObjectManager()->get(CreatorRepositoryInterface::class);
        $creator = $creatorRepository->getById($creatorId);

        return $creator;
    }
}
