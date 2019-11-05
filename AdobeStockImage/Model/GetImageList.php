<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

declare(strict_types=1);

namespace Magento\AdobeStockImage\Model;

use Magento\AdobeStockAssetApi\Api\GetAssetListInterface;
use Magento\AdobeStockImageApi\Api\ConfigInterface;
use Magento\AdobeStockImageApi\Api\GetImageListInterface;
use Magento\Framework\Api\FilterBuilder;
use Magento\Framework\Api\Search\FilterGroupBuilder;
use Magento\Framework\Api\Search\SearchCriteriaInterface;
use Magento\Framework\Api\Search\SearchResultInterface;

/**
 * Class GetImageList
 */
class GetImageList implements GetImageListInterface
{
    /**
     * Filters which skips gallery filter
     */
    private const SKIP_GALLERY_FILTERS = ['words', 'model_id', 'serie_id'];

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
     * @var ConfigInterface $config
     */
    private $config;

    /**
     * GetImageList constructor.
     *
     * @param FilterGroupBuilder $filterGroupBuilder
     * @param GetAssetListInterface $getAssetList
     * @param FilterBuilder $filterBuilder
     * @param ConfigInterface $configInterface
     * @param array $defaultFilters
     */
    public function __construct(
        FilterGroupBuilder $filterGroupBuilder,
        GetAssetListInterface $getAssetList,
        FilterBuilder $filterBuilder,
        ConfigInterface $configInterface,
        array $defaultFilters = []
    ) {
        $this->getAssetList = $getAssetList;
        $this->filterGroupBuilder = $filterGroupBuilder;
        $this->defaultFilters = $defaultFilters;
        $this->filterBuilder = $filterBuilder;
        $this->config = $configInterface;
    }

    /**
     * @inheritdoc
     */
    public function execute(SearchCriteriaInterface $searchCriteria): SearchResultInterface
    {
        $searchCriteria = $this->setDefaultGallery($searchCriteria);
        $searchCriteria = $this->setDefaultFilters($searchCriteria);

        return $this->getAssetList->execute($searchCriteria);
    }

    /**
     * Set 'gallery_id' filter if not set other 'words' filter.
     *
     * @param SearchCriteriaInterface $searchCriteria
     * @return SearchCriteriaInterface
     */
    private function setDefaultGallery(SearchCriteriaInterface $searchCriteria): SearchCriteriaInterface
    {
        $galleryId = $this->config->getDefaultGalleryId();
        if (empty($galleryId)) {
            return $searchCriteria;
        }

        $skipGalleryFilter = false;
        $filterGroups = $searchCriteria->getFilterGroups();
        foreach ($filterGroups as $filterGroup) {
            foreach ($filterGroup->getFilters() as $filter) {
                $field = $filter->getField();
                if (in_array($field, self::SKIP_GALLERY_FILTERS)) {
                    $skipGalleryFilter = true;
                }
            }
        }

        if (!$skipGalleryFilter) {
            $galleryFilter[] = $this->filterBuilder
                ->setField('gallery_id')
                ->setConditionType('eq')
                ->setValue($galleryId)
                ->create();

            $filterGroups[] = $this->filterGroupBuilder->setFilters($galleryFilter)->create();
            $searchCriteria->setFilterGroups($filterGroups);
        }
        return $searchCriteria;
    }

    /**
     * Setting the default filter states for SDK:
     *
     * @param SearchCriteriaInterface $searchCriteria
     * @return SearchCriteriaInterface
     */
    private function setDefaultFilters(SearchCriteriaInterface $searchCriteria): SearchCriteriaInterface
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
     * Get already applied filter types
     *
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
