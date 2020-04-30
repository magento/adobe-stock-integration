<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Magento\MediaGalleryUi\Model\Listing;

use Magento\Framework\Api\FilterBuilder;
use Magento\Framework\Api\Search\ReportingInterface;
use Magento\Framework\Api\Search\SearchCriteria;
use Magento\Framework\Api\Search\SearchCriteriaBuilder;
use Magento\Framework\View\Element\UiComponent\DataProvider\SearchResult;
use Magento\Framework\App\RequestInterface;
use Magento\MediaGalleryUi\Model\SelectModifierInterface;
use Magento\Framework\Api\Filter;
use Magento\Framework\View\Element\UiComponent\DataProvider\DataProvider as UiComponentDataProvider;

/**
 * Media gallery UI data provider. Try catch added for displaying errors in grid
 */
class DataProvider extends UiComponentDataProvider
{
    private const FULLTEXT_CONDITION_TYPE = 'fulltext';

    /**
     * @var SearchCriteria
     */
    protected $searchCriteria;

    /**
     * @var SelectModifierInterface
     */
    private $selectModifier;

    /**
     * @var Filter|null
     */
    private $fulltextFilter;

    /**
     * @param string $name
     * @param string $primaryFieldName
     * @param string $requestFieldName
     * @param ReportingInterface $reporting
     * @param SearchCriteriaBuilder $searchCriteriaBuilder
     * @param RequestInterface $request
     * @param FilterBuilder $filterBuilder
     * @param SelectModifierInterface $selectModifier
     * @param array $meta
     * @param array $data
     * @SuppressWarnings(PHPMD.ExcessiveParameterList)
     */
    public function __construct(
        $name,
        $primaryFieldName,
        $requestFieldName,
        ReportingInterface $reporting,
        SearchCriteriaBuilder $searchCriteriaBuilder,
        RequestInterface $request,
        FilterBuilder $filterBuilder,
        SelectModifierInterface $selectModifier,
        array $meta = [],
        array $data = []
    ) {
        parent::__construct(
            $name,
            $primaryFieldName,
            $requestFieldName,
            $reporting,
            $searchCriteriaBuilder,
            $request,
            $filterBuilder,
            $meta,
            $data
        );

        $this->selectModifier = $selectModifier;
    }

    /**
     * @inheritdoc
     */
    public function getData(): array
    {
        try {
            /** @var SearchResult $searchResult */
            $searchResult = $this->getSearchResult();

            $this->prepareSearchCriteria();
            $this->selectModifier->apply($searchResult->getSelect(), $this->getSearchCriteria());

            return $this->searchResultToOutput($searchResult);
        } catch (\Exception $exception) {
            return [
                'items' => [],
                'totalRecords' => 0,
                'errorMessage' => $exception->getMessage()
            ];
        }
    }

    /**
     * @inheritdoc
     */
    public function addFilter(Filter $filter)
    {
        if ($filter->getConditionType() === self::FULLTEXT_CONDITION_TYPE) {
            $this->fulltextFilter = $filter;
        } else {
            parent::addFilter($filter);
        }
    }

    /**
     * Add fulltext filter to SearchCriteriaBuilder and reset SearchCriteria
     *
     * @return void
     */
    private function prepareSearchCriteria(): void
    {
        if ($this->fulltextFilter) {
            $this->searchCriteria = null;
            parent::addFilter($this->fulltextFilter);
        }
    }
}
