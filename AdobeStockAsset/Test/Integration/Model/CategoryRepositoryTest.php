<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

declare(strict_types=1);

namespace Magento\AdobeStockAsset\Test\Integration\Model;

use Magento\AdobeStockAssetApi\Api\CategoryRepositoryInterface;
use Magento\AdobeStockAssetApi\Api\Data\AssetSearchResultsInterface;
use Magento\AdobeStockAssetApi\Api\Data\CategoryInterface;
use Magento\AdobeStockAssetApi\Api\Data\CategoryInterfaceFactory;
use Magento\Framework\Api\FilterBuilder;
use Magento\Framework\Api\Search\FilterGroupBuilder;
use Magento\Framework\Api\Search\SearchCriteriaBuilder;
use Magento\Framework\Api\SortOrder;
use Magento\Framework\Api\SortOrderBuilder;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\TestFramework\Helper\Bootstrap;
use PHPUnit\Framework\TestCase;

/**
 * Provide integration tests for the Adobe Stock CategoryRepository functionality.
 */
class CategoryRepositoryTest extends TestCase
{
    private const FIXTURE_ASSET_CATEGORY_ID = 42;

    /**
     * @var CategoryRepositoryInterface
     */
    private $categoryRepository;

    /**
     * @inheritdoc
     */
    protected function setUp(): void
    {
        $this->categoryRepository = Bootstrap::getObjectManager()->get(CategoryRepositoryInterface::class);
    }

    /**
     * Test getting an Adobe Stock category by id.
     *
     * @magentoDataFixture ../../../../app/code/Magento/AdobeStockAsset/Test/_files/category.php
     */
    public function testGetById(): void
    {
        /** @var CategoryInterface $expectedCategory */
        $expectedCategory = $this->categoryRepository->getById(self::FIXTURE_ASSET_CATEGORY_ID);

        $this->assertEquals($expectedCategory->getId(), self::FIXTURE_ASSET_CATEGORY_ID);
    }

    /**
     * Test delete an Adobe Stock category by id.
     *
     * @magentoDataFixture ../../../../app/code/Magento/AdobeStockAsset/Test/_files/category.php
     */
    public function testDeleteById(): void
    {
        $this->expectException(NoSuchEntityException::class);
        $this->categoryRepository->deleteById(self::FIXTURE_ASSET_CATEGORY_ID);
        $this->categoryRepository->getById(self::FIXTURE_ASSET_CATEGORY_ID);
    }

    /**
     * Test getting a list of Adobe Stock categories by search criteria.
     *
     * @magentoDataFixture ../../../../app/code/Magento/AdobeStockAsset/Test/_files/category.php
     */
    public function testGetList(): void
    {
        /** @var SearchCriteriaBuilder $searchCriteriaBuilder */
        $searchCriteriaBuilder = Bootstrap::getObjectManager()->create(SearchCriteriaBuilder::class);

        $searchCriteriaBuilder->setPageSize(2);
        $searchCriteriaBuilder->setCurrentPage(1);

        /** @var FilterBuilder $filterBuilder */
        $filterBuilder = Bootstrap::getObjectManager()->create(FilterBuilder::class);
        $idFilter = $filterBuilder->setField('id')
            ->setValue(self::FIXTURE_ASSET_CATEGORY_ID)
            ->setConditionType('eq')
            ->create();

        /** @var FilterGroupBuilder $filterGroupBuilder */
        $filterGroupBuilder = Bootstrap::getObjectManager()->create(FilterGroupBuilder::class);
        $filterGroup = $filterGroupBuilder->addFilter($idFilter)->create();

        /** @var SortOrderBuilder $sortOrderBuilder */
        $sortOrderBuilder = Bootstrap::getObjectManager()->create(SortOrderBuilder::class);

        /** @var SortOrder $sortOrder */
        $sortOrder = $sortOrderBuilder->setField('id')
            ->setDirection(SortOrder::SORT_DESC)
            ->create();

        $searchCriteria = $searchCriteriaBuilder->create();
        $searchCriteria->setSortOrders([$sortOrder]);
        $searchCriteria->setFilterGroups([$filterGroup]);

        /** @var AssetSearchResultsInterface $searchResult */
        $searchResult = $this->categoryRepository->getList($searchCriteria);

        $this->assertEquals(1, $searchResult->getTotalCount());
        $this->assertEquals(
            $searchResult->getItems()[self::FIXTURE_ASSET_CATEGORY_ID]->getId(),
            self::FIXTURE_ASSET_CATEGORY_ID
        );
    }

    /**
     * Test saving an Adobe Stock category.
     */
    public function testSave(): void
    {
        $objectManager = Bootstrap::getObjectManager();
        $categoryFactory = $objectManager->get(CategoryInterfaceFactory::class);
        /** @var CategoryInterface $category */
        $category = $categoryFactory->create(
            [
                'data' => [
                    'id' => self::FIXTURE_ASSET_CATEGORY_ID,
                    'name' => 'Test category'
                ]
            ]
        );
        $this->categoryRepository->save($category);
        $expectedCategory = $this->categoryRepository->getById(self::FIXTURE_ASSET_CATEGORY_ID);
        $this->assertEquals($expectedCategory->getId(), self::FIXTURE_ASSET_CATEGORY_ID);
        $this->categoryRepository->deleteById(self::FIXTURE_ASSET_CATEGORY_ID);
    }
}
