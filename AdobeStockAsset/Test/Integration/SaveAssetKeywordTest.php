<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Magento\AdobeStockAsset\Test\Integration;

use Magento\AdobeStockAssetApi\Api\Data\AssetInterface;
use Magento\AdobeStockAssetApi\Api\AssetRepositoryInterface;
use Magento\AdobeStockAssetApi\Api\Data\CategoryInterface;
use Magento\AdobeStockAssetApi\Api\CategoryRepositoryInterface;
use Magento\AdobeStockAssetApi\Api\Data\CreatorInterface;
use Magento\AdobeStockAssetApi\Api\CreatorRepositoryInterface;
use Magento\AdobeStockAssetApi\Api\Data\KeywordInterface;
use Magento\AdobeStockAssetApi\Api\AssetKeywordRepositoryInterface;
use Magento\Framework\App\ResourceConnection;
use Magento\TestFramework\Helper\Bootstrap;
use PHPUnit\Framework\TestCase;

/**
 * Class SaveAssetKeywordTest
 */
class SaveAssetKeywordTest extends TestCase
{
    /**
     * @var AssetKeywordRepositoryInterface
     */
    private $assetKeywordsRepository;

    /**
     * @var AssetRepositoryInterface
     */
    private $assetRepository;

    /**
     * @var CategoryRepositoryInterface
     */
    private $categoryRepository;

    /**
     * @var CreatorRepositoryInterface
     */
    private $creatorRepository;

    /**
     * @var ResourceConnection
     */
    private $resourceConnection;

    /**
     * @inheritdoc
     */
    protected function setUp()
    {
        $this->assetRepository = Bootstrap::getObjectManager()->get(AssetRepositoryInterface::class);
        $this->categoryRepository = Bootstrap::getObjectManager()->get(CategoryRepositoryInterface::class);
        $this->creatorRepository = Bootstrap::getObjectManager()->get(CreatorRepositoryInterface::class);
        $this->assetKeywordsRepository = Bootstrap::getObjectManager()->get(AssetKeywordRepositoryInterface::class);
        $this->resourceConnection = Bootstrap::getObjectManager()->get(ResourceConnection::class);
        $this->resourceConnection->getConnection()->beginTransaction();
    }

    /**
     * Rollback the test data transaction
     */
    protected function tearDown()
    {
        $this->resourceConnection->getConnection()->rollBack();
    }

    /**
     * @dataProvider testSaveDataProvider
     * @throws \Magento\Framework\Exception\AlreadyExistsException
     */
    public function testSave(AssetInterface $asset)
    {
        $category = $this->categoryRepository->save($asset->getCategory());
        $asset->setCategoryId($category->getId());
        $creator = $this->creatorRepository->save($asset->getCreator());
        $asset->setCreatorId($creator->getId());

        $this->assetRepository->save($asset);
        $this->assetKeywordsRepository->saveAssetKeywords($asset);

        $savedData = [];
        $savedKeywords = $this->assetKeywordsRepository->getAssetKeywords($asset);
        /** @var KeywordInterface $savedKeyword */
        foreach ($savedKeywords as $saved) {
            $savedData[] = $saved->getKeyword();
        }

        $originalData = [];
        $originalKeywords = $asset->getKeywords();
        /** @var KeywordInterface $original */
        foreach ($originalKeywords as $original) {
            $originalData[] = $original->getKeyword();
        }

        $result = !array_diff($originalData, $savedData);
        $this->assertTrue($result);
    }

    /**
     * Generate keywords for test purpose.
     *
     * @return AssetInterface[]
     */
    public static function testSaveDataProvider(): array
    {
        /** @var KeywordInterface[] $keywords */
        $keywords =  require __DIR__ . '/../_files/keywords.php';

        /** @var CategoryInterface $category */
        $category =  require __DIR__ . '/../_files/category.php';

        /** @var CreatorInterface $creator */
        $creator =  require __DIR__ . '/../_files/creator.php';

        /** @var AssetInterface $asset */
        $asset = require __DIR__ . '/../_files/asset.php';

        $asset->setCategory($category);
        $asset->setCreator($creator);
        $asset->setKeywords($keywords);

        return [[$asset]];
    }
}
