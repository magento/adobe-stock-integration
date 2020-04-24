<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Magento\MediaGalleryUi\Model\Listing;

use Magento\Framework\Api\FilterBuilder;
use Magento\Framework\Api\Search\ReportingInterface;
use Magento\Framework\Api\Search\SearchCriteriaBuilder;
use Magento\Framework\View\Element\UiComponent\DataProvider\SearchResult;
use Magento\Framework\App\RequestInterface;
use Magento\MediaGalleryUi\Model\SelectModifierInterface;
use Magento\Framework\View\Element\UiComponent\DataProvider\DataProvider as UiComponentDataProvider;

/**
 * Media gallery UI data provider. Try catch added for displaying errors in grid
 */
class DataProvider extends UiComponentDataProvider
{
    /**
     * @var SelectModifierInterface
     */
    private $selectModifier;

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
}
