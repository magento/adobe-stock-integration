<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Magento\AdobeStockImage\Test\Unit\Model;

use Magento\AdobeStockAssetApi\Api\GetAssetListInterface;
use Magento\AdobeStockImage\Model\GetImageList;
use Magento\Framework\Api\FilterBuilder;
use Magento\Framework\Api\Search\FilterGroupBuilder;
use Magento\Framework\Api\Search\SearchCriteriaInterface;
use Magento\Framework\Api\Search\SearchResultInterface;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\TestFramework\Unit\Helper\ObjectManager;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Magento\Framework\Api\Search\FilterGroup;
use Magento\Framework\Api\Filter;

/**
 * Test for Magento\AdobeStockImage\Model\GetImageList Model.
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
     * @inheritdoc
     */
    public function setUp()
    {
        $objectManager = new ObjectManager($this);
        $this->getAssetListMock = $this->createMock(GetAssetListInterface::class);
        $this->filterGroupBuilderMock = $this->createMock(FilterGroupBuilder::class);
        $this->filterBuilderMock = $this->createMock(FilterBuilder::class);

        $this->getImageListModel = $objectManager->getObject(
            GetImageList::class,
            [
                'getAssetList' => $this->getAssetListMock,
                'filterGroupBuilder' => $this->filterGroupBuilderMock,
                'filterBuilder' => $this->filterBuilderMock,
                'defaultFilters' => self::DEFAULT_FILTERS
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
    public function testWithDefaultFilters(array $appliedFilterNames)
    {
        $appliedFilterGroup = $this->getAppliedFilterGroup($appliedFilterNames);

        /** @var MockObject|SearchCriteriaInterface $searchCriteria */
        $searchCriteria = $this->createMock(SearchCriteriaInterface::class);
        $searchCriteria->expects($this->once())
            ->method('getFilterGroups')
            ->willReturn([$appliedFilterGroup]);
        $searchCriteria->expects($this->once())
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
    public function appliedFilterNamesProvider()
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
                []
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
            /** @var \Magento\Framework\Api\Filter|MockObject $filter */
            $filter = $this->createMock(Filter::class);
            $filter->expects($this->once())
                ->method('getField')
                ->willReturn($field);
            $filters[] = $filter;
        }

        /** @var FilterGroup|MockObject $filterGroup */
        $filterGroup = $this->createMock(FilterGroup::class);
        $filterGroup->expects($this->once())
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
        $filterBuilderCallIndex = 0;

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

        $this->filterGroupBuilderMock->expects($this->once())
            ->method('setFilters')
            ->with($filters)
            ->willReturnSelf();
        $this->filterGroupBuilderMock->expects($this->once())
            ->method('create')
            ->willReturn($filterGroup);

        return $filterGroup;
    }
}
