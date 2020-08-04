<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

declare(strict_types=1);

namespace Magento\AdobeStockAsset\Test\Integration\Model;

use Magento\AdobeStockAssetApi\Api\CreatorRepositoryInterface;
use Magento\AdobeStockAssetApi\Api\Data\AssetSearchResultsInterface;
use Magento\AdobeStockAssetApi\Api\Data\CreatorInterface;
use Magento\AdobeStockAssetApi\Api\Data\CreatorInterfaceFactory;
use Magento\Framework\Api\FilterBuilder;
use Magento\Framework\Api\Search\FilterGroupBuilder;
use Magento\Framework\Api\Search\SearchCriteriaBuilder;
use Magento\Framework\Api\SortOrder;
use Magento\Framework\Api\SortOrderBuilder;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\TestFramework\Helper\Bootstrap;
use PHPUnit\Framework\TestCase;

/**
 * Provide integration tests for the Adobe Stock CreatorRepository functionality.
 */
class CreatorRepositoryTest extends TestCase
{
    private const FIXTURE_ASSET_CREATOR_ID = 42;

    /**
     * @var CreatorRepositoryInterface
     */
    private $creatorRepository;

    /**
     * @inheritdoc
     */
    protected function setUp(): void
    {
        $this->creatorRepository = Bootstrap::getObjectManager()->get(CreatorRepositoryInterface::class);
    }

    /**
     * Test getting an Adobe Stock creator by id.
     *
     * @magentoDataFixture ../../../../app/code/Magento/AdobeStockAsset/Test/_files/creator.php
     */
    public function testGetById(): void
    {
        /** @var CreatorInterface $expectedCreator */
        $expectedCreator = $this->creatorRepository->getById(self::FIXTURE_ASSET_CREATOR_ID);

        $this->assertEquals($expectedCreator->getId(), self::FIXTURE_ASSET_CREATOR_ID);
    }

    /**
     * Test delete an Adobe Stock creator by id.
     *
     * @magentoDataFixture ../../../../app/code/Magento/AdobeStockAsset/Test/_files/creator.php
     */
    public function testDeleteById(): void
    {
        $this->expectException(NoSuchEntityException::class);
        $this->creatorRepository->deleteById(self::FIXTURE_ASSET_CREATOR_ID);
        $this->creatorRepository->getById(self::FIXTURE_ASSET_CREATOR_ID);
    }

    /**
     * Test getting a list of Adobe Stock creators by search criteria.
     *
     * @magentoDataFixture ../../../../app/code/Magento/AdobeStockAsset/Test/_files/creator.php
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
            ->setValue(self::FIXTURE_ASSET_CREATOR_ID)
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
        $searchResult = $this->creatorRepository->getList($searchCriteria);

        $this->assertEquals(1, $searchResult->getTotalCount());
        $this->assertEquals(
            $searchResult->getItems()[self::FIXTURE_ASSET_CREATOR_ID]->getId(),
            self::FIXTURE_ASSET_CREATOR_ID
        );
    }

    /**
     * Test saving an Adobe Stock creator.
     */
    public function testSave(): void
    {
        $objectManager = Bootstrap::getObjectManager();

        /** @var CreatorInterfaceFactory $creatorFactory */
        $creatorFactory = $objectManager->get(CreatorInterfaceFactory::class);
        /** @var CreatorInterface $creator */
        $creator = $creatorFactory->create(
            [
                'data' => [
                    'id' => 42,
                    'name' => 'Test creator'
                ]
            ]
        );
        $this->creatorRepository->save($creator);
        $expectedCreator = $this->creatorRepository->getById(self::FIXTURE_ASSET_CREATOR_ID);
        $this->assertEquals($expectedCreator->getId(), self::FIXTURE_ASSET_CREATOR_ID);
        $this->creatorRepository->deleteById(self::FIXTURE_ASSET_CREATOR_ID);
    }
}
