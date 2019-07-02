<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Magento\AdobeStockImageAdminUi\Model\Listing;

use Magento\Framework\Api\FilterBuilder;
use Magento\Framework\Api\Search\ReportingInterface;
use Magento\Framework\Api\Search\SearchCriteriaBuilder;
use Magento\Framework\Api\SearchResultsInterface;
use Magento\Framework\App\RequestInterface;
use Magento\AdobeStockImageApi\Api\GetImageListInterface;
use Magento\Ui\DataProvider\SearchResultFactory;

/**
 * DataProvider of customer addresses for customer address grid.
 */
class DataProvider extends \Magento\Framework\View\Element\UiComponent\DataProvider\DataProvider
{
    const FIELD_WORDS = 'words';

    /**
     * @var GetImageListInterface
     */
    private $getImageList;

    /**
     * @var SearchResultFactory
     */
    private $searchResultFactory;

    /**
     * DataProvider constructor.
     * @param string $name
     * @param string $primaryFieldName
     * @param string $requestFieldName
     * @param ReportingInterface $reporting
     * @param SearchCriteriaBuilder $searchCriteriaBuilder
     * @param RequestInterface $request
     * @param FilterBuilder $filterBuilder
     * @param GetImageListInterface $getImageList
     * @param SearchResultFactory $searchResultFactory
     * @param array $meta
     * @param array $data
     */
    public function __construct(
        string $name,
        string $primaryFieldName,
        string $requestFieldName,
        ReportingInterface $reporting,
        SearchCriteriaBuilder $searchCriteriaBuilder,
        RequestInterface $request,
        FilterBuilder $filterBuilder,
        GetImageListInterface $getImageList,
        SearchResultFactory $searchResultFactory,
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
        $this->getImageList = $getImageList;
        $this->searchResultFactory = $searchResultFactory;
    }

    /**
     * @inheritdoc
     */
    public function addFilter(\Magento\Framework\Api\Filter $filter)
    {
        if (self::FIELD_WORDS === $filter->getField()) {
            $value = str_replace(['"', '\\'], '', $filter->getValue());
            $filter->setValue($value);
        }

        if (self::FIELD_WORDS !== $filter->getField() || $filter->getValue()) {
            parent::addFilter($filter);
        }
    }

    /**
     * @return SearchResultsInterface
     */
    public function getSearchResult()
    {
        $result = $this->getImageList->execute($this->getSearchCriteria());
        return $this->searchResultFactory->create(
            $result->getItems(),
            $result->getTotalCount(),
            $this->getSearchCriteria(),
            'id'
        );
    }
}
