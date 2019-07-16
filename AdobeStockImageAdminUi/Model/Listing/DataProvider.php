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
use Magento\Framework\App\RequestInterface;
use Magento\AdobeStockImageApi\Api\GetImageListInterface;
use Magento\Framework\Exception\LocalizedException;
use Magento\Ui\DataProvider\SearchResultFactory;
use \Magento\Framework\UrlInterface;

/**
 * DataProvider of customer addresses for customer address grid.
 */
class DataProvider extends \Magento\Framework\View\Element\UiComponent\DataProvider\DataProvider
{
    /**
     * @var GetImageListInterface
     */
    private $getImageList;

    /**
     * @var SearchResultFactory
     */
    private $searchResultFactory;

    /**
     * @var UrlInterface
     */
    private $urlBuilder;

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
     * @param UrlInterface $urlBuilder
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
        UrlInterface $urlBuilder,
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
        $this->urlBuilder = $urlBuilder;
    }

    /**
     * @inheritdoc
     */
    public function getSearchResult()
    {
        try {
            $result = $this->getImageList->execute($this->getSearchCriteria());
            return $this->searchResultFactory->create(
                $result->getItems(),
                $result->getTotalCount(),
                $this->getSearchCriteria(),
                'id'
            );
        } catch (\Exception $e) {
            $message = __('Error with message: %1', $e->getMessage());
            if ($e->getCode() === 403) {
                $message = __(
                    'You have problem with API key, please check it on <a href="%url'
                    . '#system_adobe_stock_integration-link"'
                    . '>Adobe Stock Integration configuration</a> page.',
                    ['url' => $this->urlBuilder->getUrl('adminhtml/system_config/edit/section/system')]
                );
            }
            throw new LocalizedException($message, $e, $e->getCode());
        }
    }
}
