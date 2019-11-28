<?php

namespace Magento\MediaGalleryUi\Model\Listing;

use Magento\Framework\Api\FilterBuilder;
use Magento\Framework\Api\Search\ReportingInterface;
use Magento\Framework\Api\Search\SearchCriteriaBuilder;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\View\Element\UiComponent\DataProvider\DataProvider as UiComponentDataProvider;
use Magento\MediaGalleryUi\Model\ImagesProvider;

class DataProvider extends UiComponentDataProvider
{
    /** @var ImagesProvider */
    private $imagesProvider;

    public function __construct(
        $name,
        $primaryFieldName,
        $requestFieldName,
        ReportingInterface $reporting,
        SearchCriteriaBuilder $searchCriteriaBuilder,
        RequestInterface $request,
        FilterBuilder $filterBuilder,
        array $meta = [],
        array $data = [],
        ImagesProvider $imagesProvider
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
    }

    private function getErrorData(): array
    {
        return [
            'items' => [],
            'totalRecords' => 0,
            'errorMessage' => 'Error'
        ];
    }


    public function getData(): array
    {
        return $this->imagesProvider->getImages();
    }
}
