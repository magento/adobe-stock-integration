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
use Magento\Framework\Api\Search\SearchResultInterface;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\View\Element\UiComponent\DataProvider\DataProvider as UiComponentDataProvider;
use Magento\MediaGalleryUi\Model\Filesystem\ImagesProvider;
use Psr\Log\LoggerInterface;

/**
 * Media gallery UI data provider
 */
class DataProvider extends UiComponentDataProvider
{
    /**
     * @var ImagesProvider
     */
    private $imagesProvider;

    /**
     * @var LoggerInterface
     */
    private $logger;

    /**
     * DataProvider constructor.
     *
     * @param string $name
     * @param string $primaryFieldName
     * @param string $requestFieldName
     * @param ReportingInterface $reporting
     * @param SearchCriteriaBuilder $searchCriteriaBuilder
     * @param RequestInterface $request
     * @param FilterBuilder $filterBuilder
     * @param ImagesProvider $imagesProvider
     * @param LoggerInterface $logger
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
        ImagesProvider $imagesProvider,
        LoggerInterface $logger,
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
        $this->imagesProvider = $imagesProvider;
        $this->logger = $logger;
    }

    /**
     * @inheritdoc
     */
    public function getData(): array
    {
        try {
            return $this->searchResultToOutput($this->getSearchResult());
        } catch (\Exception $exception) {
            $this->logger->critical($exception);
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
        return $this->imagesProvider->getImages($this->getSearchCriteria());
    }
}
