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
use Magento\Framework\Api\Search\SearchResultInterfaceFactory;
use Magento\Framework\Exception\LocalizedException;

/**
 * DataProvider of customer addresses for customer address grid.
 */
class DataProvider extends \Magento\Framework\View\Element\UiComponent\DataProvider\DataProvider
{
    /**
     * @var SearchResultInterfaceFactory
     */
    private $searchResultFactory;

    /**
     * @var GetImageListInterface
     */
    private $getImageList;

    /**
     * @var string|null
     */
    private $errorMessage;

    /**
     * DataProvider constructor.
     * @param string                $name
     * @param string                $primaryFieldName
     * @param string                $requestFieldName
     * @param ReportingInterface    $reporting
     * @param SearchCriteriaBuilder $searchCriteriaBuilder
     * @param RequestInterface      $request
     * @param FilterBuilder         $filterBuilder
     * @param GetImageListInterface $getImageList
     * @param array                 $meta
     * @param array                 $data
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
        SearchResultInterfaceFactory $searchResultFactory,
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
        $this->searchResultFactory = $searchResultFactory;
        $this->getImageList = $getImageList;
    }

    /**
     * @return string|null
     */
    public function getErrorMessage(): ?string
    {
        return $this->errorMessage;
    }

    /**
     * @param string $message
     */
    public function setErrorMessage(string $message): void
    {
        $this->errorMessage = $message;
    }

    /**
     * @inheritDoc
     */
    public function getData()
    {
        $recordsData = $this->searchResultToOutput($this->getSearchResult());
        $errorMessage = ['errorMessage' => $this->getErrorMessage()];

        return array_merge($recordsData, $errorMessage);
    }

    /**
     * @inheritdoc
     */
    public function getSearchResult()
    {
        try {
            $searchResult = $this->getImageList->execute($this->getSearchCriteria());
        } catch (LocalizedException $exception) {
            $this->setErrorMessage($exception->getMessage());
            $searchResult = $this->searchResultFactory->create();
            $searchResult->setItems([]);
        }

        return $searchResult;
    }
}
