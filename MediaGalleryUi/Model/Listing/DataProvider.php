<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Magento\MediaGalleryUi\Model\Listing;

use Magento\Framework\Api\Filter;
use Magento\Framework\View\Element\UiComponent\DataProvider\SearchResult;
use Magento\Framework\View\Element\UiComponent\DataProvider\DataProvider as UiComponentDataProvider;

/**
 * Media gallery UI data provider. Try catch added for displaying errors in grid
 */
class DataProvider extends UiComponentDataProvider
{
    /**
     * @var Filter|null
     */
    private $fulltextFilter;

    /**
     * @var array
     */
    private $columns = ['name'];

    /**
     * @inheritdoc
     */
    public function getData(): array
    {
        try {
            return $this->searchResultToOutput($this->getPreparedSearchResult());
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
        if ($filter->getField() === 'fulltext' && $filter->getValue() !== '') {
            $this->fulltextFilter = $filter;
        }

        parent::addFilter($filter);
    }

    /**
     * Returns search result
     *
     * @return SearchResult
     */
    private function getPreparedSearchResult(): SearchResult
    {
        $searchResult = $this->getSearchResult();
        if (isset($this->fulltextFilter)) {
            $this->applyOrFilters($searchResult);
        }

        return $searchResult;
    }

    /**
     * Apply OR filters
     *
     * @param SearchResult $searchResult
     * @return void
     */
    private function applyOrFilters(SearchResult $searchResult): void
    {
        foreach ($this->columns as $column) {
            $searchResult->getSelect()
                ->orWhere(
                    $column . ' LIKE ?',
                    sprintf('%%%s%%', $this->fulltextFilter->getValue())
                );
        }
    }
}
