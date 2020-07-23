<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Magento\AdobeStockImageAdminUi\Ui\Component\Listing\Columns;

use Magento\AdobeStockAssetApi\Api\AssetRepositoryInterface;
use Magento\AdobeStockAssetApi\Api\Data\AssetInterface;
use Magento\Framework\Api\SearchCriteriaBuilder;
use Magento\Framework\View\Element\UiComponent\ContextInterface;
use Magento\Framework\View\Element\UiComponentFactory;
use Magento\Ui\Component\Listing\Columns\Column;

/**
 * Unlicensed Overlay column
 */
class UnlicensedOverlay extends Column
{
    /**
     * @var AssetRepositoryInterface
     */
    private $assetRepository;

    /**
     * @var SearchCriteriaBuilder
     */
    private $searchCriteriaBuilder;

    /**
     * UnlicensedOverlay constructor.
     * @param ContextInterface $context
     * @param UiComponentFactory $uiComponentFactory
     * @param AssetRepositoryInterface $assetRepository
     * @param SearchCriteriaBuilder $searchCriteriaBuilder
     * @param array $components
     * @param array $data
     */
    public function __construct(
        ContextInterface $context,
        UiComponentFactory $uiComponentFactory,
        AssetRepositoryInterface $assetRepository,
        SearchCriteriaBuilder $searchCriteriaBuilder,
        array $components = [],
        array $data = []
    ) {
        parent::__construct($context, $uiComponentFactory, $components, $data);
        $this->assetRepository = $assetRepository;
        $this->searchCriteriaBuilder = $searchCriteriaBuilder;
    }

    /**
     * Prepare Data Source
     *
     * @param array $dataSource
     *
     * @return array
     */
    public function prepareDataSource(array $dataSource): array
    {
        if (isset($dataSource['data']['items'])) {
            $ids = array_map(function ($item) {
                return $item['id'];
            }, $dataSource['data']['items']);
            $licensedData = $this->getLicensedData($ids);
            foreach ($dataSource['data']['items'] as & $item) {
                $item[$this->getData('name')] = isset($licensedData[$item['id']]) && !$licensedData[$item['id']]
                    ? 'Unlicensed'
                    : '';
            }
        }

        return $dataSource;
    }

    /**
     * Get licensed data from array of ids
     *
     * @param array $ids
     * @return array
     */
    private function getLicensedData(array $ids): array
    {
        $licensedData = [];
        $assetArray = $this->getAssetArrayByIds($ids);
        /** @var AssetInterface $asset */
        foreach ($assetArray as $asset) {
            $licensedData[$asset->getMediaGalleryId()] = $asset->getIsLicensed();
        }

        return $licensedData;
    }

    /**
     * Get asset array by ids
     *
     * @param array $ids
     * @return array
     */
    private function getAssetArrayByIds(array $ids): array
    {
        $searchCriteria = $this->searchCriteriaBuilder
            ->addFilter('media_gallery_id', $ids, 'in')
            ->create();
        $result = $this->assetRepository->getList($searchCriteria);

        return $result->getItems();
    }
}
