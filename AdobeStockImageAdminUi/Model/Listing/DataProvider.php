<?php
declare(strict_types=1);
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magento\AdobeStockImageAdminUi\Model\Listing;

use Magento\AdobeStockImage\Model\GetImageList;
use Magento\Framework\Api\FilterBuilder;
use Magento\Framework\Api\Search\ReportingInterface;
use Magento\Framework\Api\Search\SearchCriteriaBuilder;
use Magento\Framework\Api\SearchResultsInterface;
use Magento\Framework\App\RequestInterface;

/**
 * Dataprovider of customer addresses for customer address grid.
 */
class DataProvider extends \Magento\Framework\View\Element\UiComponent\DataProvider\DataProvider
{
    /**
     * @var GetImageList
     */
    private $getImageList;

    public function __construct(
        $name,
        $primaryFieldName,
        $requestFieldName,
        ReportingInterface $reporting,
        SearchCriteriaBuilder $searchCriteriaBuilder,
        RequestInterface $request,
        FilterBuilder $filterBuilder,
        GetImageList $getImageList,
        array $meta = [],
        array $data = []
    ) {
        $this->getImageList = $getImageList;
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
    }

    /**
     * @return SearchResultsInterface
     */
    public function getSearchResult()
    {
        return $this->getImageList->execute($this->getSearchCriteria());
    }
}
