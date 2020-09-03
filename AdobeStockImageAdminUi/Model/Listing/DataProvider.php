<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

declare(strict_types=1);

namespace Magento\AdobeStockImageAdminUi\Model\Listing;

use Magento\AdobeStockImageApi\Api\GetImageListInterface;
use Magento\Framework\Api\FilterBuilder;
use Magento\Framework\Api\Search\ReportingInterface;
use Magento\Framework\Api\Search\SearchCriteriaBuilder;
use Magento\Framework\Api\Search\SearchResultInterface;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\Exception\AuthenticationException;
use Magento\Framework\UrlInterface;
use Magento\Framework\View\Element\UiComponent\DataProvider\DataProvider as UiComponentDataProvider;

/**
 * DataProvider of customer addresses for customer address grid.
 */
class DataProvider extends UiComponentDataProvider
{
    /**
     * @var GetImageListInterface
     */
    private $getImageList;

    /**
     * @var UrlInterface
     */
    private $url;

    /**
     * @param string $name
     * @param string $primaryFieldName
     * @param string $requestFieldName
     * @param ReportingInterface $reporting
     * @param SearchCriteriaBuilder $searchCriteriaBuilder
     * @param RequestInterface $request
     * @param FilterBuilder $filterBuilder
     * @param GetImageListInterface $getImageList
     * @param UrlInterface $url
     * @param array $meta
     * @param array $data
     * @SuppressWarnings(PHPMD.ExcessiveParameterList)
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
        UrlInterface $url,
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
        $this->url = $url;
    }

    /**
     * @inheritdoc
     */
    public function getData()
    {
        try {
            return $this->searchResultToOutput($this->getSearchResult());
        } catch (AuthenticationException $exception) {
            return [
                'items' => [],
                'totalRecords' => 0,
                'errorMessage' => __(
                    'Failed to authenticate to Adobe Stock API. <br> Please correct the API credentials in '
                    . '<a href="%url">Configuration → System → Adobe Stock Integration.</a>',
                    [
                        'url' => $this->url->getUrl(
                            'adminhtml/system_config/edit',
                            [
                                'section' => 'system',
                                '_fragment' => 'system_adobe_stock_integration-link'
                            ]
                        )
                    ]
                )
            ];
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
    public function getSearchResult(): SearchResultInterface
    {
        return $this->getImageList->execute($this->getSearchCriteria());
    }
}
