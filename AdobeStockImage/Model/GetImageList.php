<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

declare(strict_types=1);

namespace Magento\AdobeStockImage\Model;

use Magento\AdobeStockAssetApi\Api\GetAssetListInterface;
use Magento\AdobeStockImageApi\Api\GetImageListInterface;
use Magento\Framework\Api\Search\SearchResultInterface;
use Magento\Framework\Api\Search\SearchCriteriaInterface;
use Magento\Framework\Api\Search\FilterGroupBuilder;
use Magento\Framework\Api\FilterBuilder;

/**
 * Class GetImageList
 */
class GetImageList implements GetImageListInterface
{
    /**
     * @var GetAssetListInterface
     */
    private $getAssetList;

    /**
     * @var array $defaultFilter
     */
    private $defaultFilters;

    /**
     * @var FilterGroupBuilder
     */
    private $filterGroupBuilder;

    /**
     * @var FilterBuilder
     */
    private $filterBuilder;

    /**
     * GetImageList constructor.
     *
     * @param FilterGroupBuilder $filterGroupBuilder
     * @param GetAssetListInterface $getAssetList
     * @param FilterBuilder $filterBuilder
     * @param array $defaultFilters
     */
    public function __construct(
        FilterGroupBuilder $filterGroupBuilder,
        GetAssetListInterface $getAssetList,
        FilterBuilder $filterBuilder,
        array $defaultFilters = []
    ) {
        $this->getAssetList = $getAssetList;
        $this->filterGroupBuilder = $filterGroupBuilder;
        $this->defaultFilters = $defaultFilters;
        $this->filterBuilder = $filterBuilder;
    }

    /**
     * @inheritdoc
     */
    public function execute(SearchCriteriaInterface $searchCriteria): SearchResultInterface
    {
        $searchCriteria = $this->setDefaultFilters($searchCriteria);
        return $this->getAssetList->execute($searchCriteria);
    }

    /**
     * Setting the default filter states for SDK:
     *
     * @param SearchCriteriaInterface $searchCriteria
     * @return SearchCriteriaInterface
     */
    private function setDefaultFilters(SearchCriteriaInterface $searchCriteria)
    {
        $filterGroups = $searchCriteria->getFilterGroups();
        $appliedFilters = $this->getAppliedFilters($filterGroups);

        foreach ($this->defaultFilters as $filter) {
            if (!in_array($filter['type'], $appliedFilters)) {
                $filters[] = $this->filterBuilder
                    ->setField($filter['type'])
                    ->setConditionType($filter['condition'])
                    ->setValue($filter['field'])
                    ->create();
            }
        }
        if (!empty($filters)) {
            $filterGroups[] = $this->filterGroupBuilder->setFilters($filters)->create();
        }
        $searchCriteria->setFilterGroups($filterGroups);
        return $searchCriteria;
    }

    /**
     * @param array $filterGroups
     * @return array
     */
    private function getAppliedFilters(array $filterGroups): array
    {
        $appliedFilters = [];
        foreach ($filterGroups as $filterGroup) {
            foreach ($filterGroup->getFilters() as $filter) {
                $appliedFilters[] = $filter->getField();
            }
        }
        return $appliedFilters;
    }
}
