<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Magento\AdobeStockImage\Test\Unit\Model;

use Magento\AdobeStockAssetApi\Api\GetAssetListInterface;
use Magento\AdobeStockImage\Model\GetImageList;
use Magento\AdobeStockImageApi\Api\ConfigInterface;
use Magento\Framework\Api\Filter;
use Magento\Framework\Api\FilterBuilder;
use Magento\Framework\Api\Search\FilterGroup;
use Magento\Framework\Api\Search\FilterGroupBuilder;
use Magento\Framework\Api\Search\SearchCriteriaInterface;
use Magento\Framework\Api\Search\SearchResultInterface;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\TestFramework\Unit\Helper\ObjectManager;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

/**
 * Test for Magento\AdobeStockImage\Model\GetImageList Model.
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class GetImageListTest extends TestCase
{
    private const DEFAULT_FILTERS = [
        'illustration_filter' => [
            'type' => 'content_type_filter',
            'condition' => 'or',
            'field' => 'illustration',
        ],
        'photo_filter' => [
            'type' => 'content_type_filter',
            'condition' => 'or',
            'field' => 'photo',
        ],
        'premium_filter' => [
            'type' => 'premium_price_filter',
            'condition' => 'eq',
            'field' => 'ALL',
        ]
    ];

    /**
     * @var GetAssetListInterface|MockObject
     */
    private $getAssetListMock;

    /**
     * @var GetImageList
     */
    private $getImageListModel;

    /**
     * @var FilterGroupBuilder|MockObject
     */
    private $filterGroupBuilderMock;

    /**
     * @var FilterBuilder|MockObject
     */
    private $filterBuilderMock;

    /**
     * @var ConfigInterface|MockObject
     */
    private $config;

    /**
     * @var bool $isAppliedGalleryFilter
     */
    private $isAppliedGalleryFilter = false;

    /**
     * @inheritdoc
     */
    protected function setUp(): void
    {
        $objectManager = new ObjectManager($this);
        $this->getAssetListMock = $this->createMock(GetAssetListInterface::class);
        $this->filterGroupBuilderMock = $this->createMock(FilterGroupBuilder::class);
        $this->filterBuilderMock = $this->createMock(FilterBuilder::class);
        $this->config = $this->createMock(ConfigInterface::class);

        $this->getImageListModel = $objectManager->getObject(
            GetImageList::class,
            [
                'getAssetList' => $this->getAssetListMock,
                'filterGroupBuilder' => $this->filterGroupBuilderMock,
                'filterBuilder' => $this->filterBuilderMock,
                'defaultFilters' => self::DEFAULT_FILTERS,
                'config' => $this->config
            ]
        );
    }

    /**
     * Test 'execute' method of GetImageList class.
     *
     * @param array $appliedFilterNames
     * @dataProvider appliedFilterNamesProvider
     * @throws LocalizedException
     */
    public function testWithDefaultFilters(array $appliedFilterNames): void
    {

        /** @var MockObject|SearchCriteriaInterface $searchCriteria */
        $searchCriteria = $this->createMock(SearchCriteriaInterface::class);
        $appliedFilterGroup = $this->getAppliedFilterGroup($appliedFilterNames);
        $this->config->expects($this->once())->method('getDefaultGalleryId')->willReturn('galleryidvalue');

        foreach ($appliedFilterNames as $appliedFilter) {
            if ($appliedFilter !== 'words') {
                $this->applyDefaultGalleryFilter($searchCriteria, $appliedFilterGroup);
            }
        }

        $searchCriteria->expects($this->any())
            ->method('getFilterGroups')
            ->willReturn([$appliedFilterGroup]);
        $searchCriteria->expects($this->at($this->isAppliedGalleryFilter ? 1 : 2))
            ->method('setFilterGroups')
            ->with([$appliedFilterGroup, $this->getDefaultFilterGroup($appliedFilterNames)]);

        $searchResult = $this->createMock(SearchResultInterface::class);

        $this->getAssetListMock->expects($this->once())
            ->method('execute')
            ->with($searchCriteria)
            ->willReturn($searchResult);

        $this->assertEquals($searchResult, $this->getImageListModel->execute($searchCriteria));
    }

    /**
     * Data provider for testWithDefaultFilters
     *
     * @return array
     */
    public function appliedFilterNamesProvider(): array
    {
        return [
            [
                ['content_type_filter']
            ],
            [
                ['words']
            ],
            [
                ['premium_price_filter']
            ],
            [
                ['']
            ]
        ];
    }

    /**
     * Get filter group with applied filters
     *
     * @param array $appliedFilterNames
     * @return FilterGroup
     */
    private function getAppliedFilterGroup(array $appliedFilterNames): FilterGroup
    {
        $filters = [];

        foreach ($appliedFilterNames as $field) {
            /** @var Filter|MockObject $filter */
            $filter = $this->createMock(Filter::class);
            $filter->expects($this->exactly(2))
                ->method('getField')
                ->willReturn($field);
            $filters[] = $filter;
        }

        /** @var FilterGroup|MockObject $filterGroup */
        $filterGroup = $this->createMock(FilterGroup::class);
        $filterGroup->expects($this->exactly(2))
            ->method('getFilters')
            ->willReturn($filters);

        return $filterGroup;
    }

    /**
     * Get filter group with default filters that should be applied
     *
     * @param array $appliedFilterNames
     * @return FilterGroup
     */
    private function getDefaultFilterGroup(array $appliedFilterNames): FilterGroup
    {
        $filters = [];
        $filterBuilderCallIndex = $this->isAppliedGalleryFilter ? 4 : 0;

        foreach (self::DEFAULT_FILTERS as $defaultFilter) {
            if (!in_array($defaultFilter['type'], $appliedFilterNames)) {
                $this->filterBuilderMock->expects($this->at($filterBuilderCallIndex++))
                    ->method('setField')
                    ->with($defaultFilter['type'])
                    ->willReturnSelf();
                $this->filterBuilderMock->expects($this->at($filterBuilderCallIndex++))
                    ->method('setConditionType')
                    ->with($defaultFilter['condition'])
                    ->willReturnSelf();
                $this->filterBuilderMock->expects($this->at($filterBuilderCallIndex++))
                    ->method('setValue')
                    ->with($defaultFilter['field'])
                    ->willReturnSelf();

                $filter = $this->createMock(Filter::class);

                $this->filterBuilderMock->expects($this->at($filterBuilderCallIndex++))
                    ->method('create')
                    ->willReturn($filter);

                $filters[] = $filter;
            }
        }

        /** @var FilterGroup|MockObject $filterGroup */
        $filterGroup = $this->createMock(FilterGroup::class);

        $this->filterGroupBuilderMock->expects($this->at($this->isAppliedGalleryFilter ? 2 : 0))
            ->method('setFilters')
            ->with($filters)
            ->willReturnSelf();
        $this->filterGroupBuilderMock->expects($this->at($this->isAppliedGalleryFilter ? 3 : 1))
            ->method('create')
            ->willReturn($filterGroup);

        return $filterGroup;
    }

    /**
     * Set's filter group with 'gallery_id' filter
     *
     * @param MockObject $searchCriteria
     * @param FilterGroup $appliedFilters
     * @return void
     */
    private function applyDefaultGalleryFilter(
        MockObject $searchCriteria,
        FilterGroup $appliedFilters
    ): void {
        $this->filterBuilderMock->expects($this->at(0))
            ->method('setField')
            ->willReturnSelf();
        $this->filterBuilderMock->expects($this->at(1))
            ->method('setConditionType')
            ->willReturnSelf();
        $this->filterBuilderMock->expects($this->at(2))
            ->method('setValue')
            ->willReturnSelf();

        $filter = $this->createMock(Filter::class);

        $this->filterBuilderMock->expects($this->at(3))
            ->method('create')
            ->willReturn($filter);

        $filterGroup = $this->createMock(FilterGroup::class);

        $this->filterGroupBuilderMock->expects($this->at(0))
            ->method('setFilters')
            ->with([$filter])
            ->willReturnSelf();
        $this->filterGroupBuilderMock->expects($this->at(1))
            ->method('create')
            ->willReturn($filterGroup);
        $searchCriteria->expects($this->at(1))
            ->method('setFilterGroups')
            ->with([$filterGroup, $appliedFilters]);

        $this->isAppliedGalleryFilter = true;
    }
}
