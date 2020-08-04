<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

declare(strict_types=1);

namespace Magento\AdobeStockAsset\Test\Integration\Model;

use Magento\AdobeStockAssetApi\Api\AssetRepositoryInterface;
use Magento\AdobeStockAssetApi\Api\Data\AssetInterface;
use Magento\AdobeStockAssetApi\Api\Data\AssetSearchResultsInterface;
use Magento\Framework\Api\FilterBuilder;
use Magento\Framework\Api\Search\FilterGroupBuilder;
use Magento\Framework\Api\Search\SearchCriteriaBuilder;
use Magento\Framework\Api\SortOrder;
use Magento\Framework\Api\SortOrderBuilder;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\TestFramework\Helper\Bootstrap;
use PHPUnit\Framework\TestCase;

/**
 * Provide integration tests for the Adobe Stock AssetRepository functionality.
 */
class AssetRepositoryTest extends TestCase
{
    private const FIXTURE_ASSET_ID = 1;

    /**
     * @var AssetRepositoryInterface
     */
    private $assetRepository;

    /**
     * @inheritdoc
     */
    protected function setUp(): void
    {
        $this->assetRepository = Bootstrap::getObjectManager()->get(AssetRepositoryInterface::class);
    }

    /**
     * Test getting an Adobe Stock asset by id.
     *
     * @magentoDataFixture ../../../../app/code/Magento/AdobeStockAsset/Test/_files/asset.php
     */
    public function testGetById(): void
    {
        /** @var AssetInterface $expectedAsset */
        $expectedAsset = $this->assetRepository->getById(self::FIXTURE_ASSET_ID);

        $this->assertEquals($expectedAsset->getId(), self::FIXTURE_ASSET_ID);
    }

    /**
     * Test delete an Adobe Stock asset by id.
     *
     * @magentoDataFixture ../../../../app/code/Magento/AdobeStockAsset/Test/_files/asset.php
     */
    public function testDeleteById(): void
    {
        $this->expectException(NoSuchEntityException::class);
        $this->assetRepository->deleteById(self::FIXTURE_ASSET_ID);
        $this->assetRepository->getById(self::FIXTURE_ASSET_ID);
    }

    /**
     * Test getting a list of Adobe Stock assets by search criteria.
     *
     * @magentoDataFixture ../../../../app/code/Magento/AdobeStockAsset/Test/_files/asset.php
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
            ->setValue(self::FIXTURE_ASSET_ID)
            ->setConditionType('eq')
            ->create();

        /** @var FilterGroupBuilder $filterGroupBuilder */
        $filterGroupBuilder = Bootstrap::getObjectManager()->create(FilterGroupBuilder::class);
        $filterGroup = $filterGroupBuilder->addFilter($idFilter)->create();

        /** @var SortOrderBuilder $sortOrderBuilder */
        $sortOrderBuilder = Bootstrap::getObjectManager()->create(SortOrderBuilder::class);

        $sortOrder = $sortOrderBuilder->setField('id')
            ->setDirection(SortOrder::SORT_DESC)
            ->create();

        $searchCriteria = $searchCriteriaBuilder->create();
        $searchCriteria->setSortOrders([$sortOrder]);
        $searchCriteria->setFilterGroups([$filterGroup]);

        /** @var AssetSearchResultsInterface $searchResult */
        $searchResult = $this->assetRepository->getList($searchCriteria);

        $this->assertEquals(1, $searchResult->getTotalCount());
        $this->assertEquals($searchResult->getItems()[self::FIXTURE_ASSET_ID]->getId(), self::FIXTURE_ASSET_ID);
    }
}
