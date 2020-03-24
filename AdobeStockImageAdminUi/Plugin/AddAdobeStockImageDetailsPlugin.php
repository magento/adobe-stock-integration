<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Magento\AdobeStockImageAdminUi\Plugin;

use Exception;
use Magento\AdobeStockAssetApi\Api\AssetRepositoryInterface;
use Magento\AdobeStockAssetApi\Api\Data\AssetInterface;
use Magento\AdobeStockAssetApi\Api\Data\AssetSearchResultsInterface;
use Magento\Framework\Api\FilterBuilder;
use Magento\Framework\Api\Search\SearchCriteriaBuilder;
use Magento\MediaGalleryUi\Model\GetImageDetailsByAssetId;
use Psr\Log\LoggerInterface;

/**
 * Plugin which adds an Adobe Stock image details
 */
class AddAdobeStockImageDetailsPlugin
{
    private const MEDIA_GALLERY_ID = 'media_gallery_id';

    /**
     * @var SearchCriteriaBuilder
     */
    private $searchCriteriaBuilder;

    /**
     * @var AssetRepositoryInterface
     */
    private $assetRepository;

    /**
     * @var FilterBuilder
     */
    private $filterBuilder;

    /**
     * @var LoggerInterface
     */
    private $logger;

    /**
     * AddAdobeStockImageDetailsPlugin constructor.
     *
     * @param FilterBuilder $filterBuilder
     * @param AssetRepositoryInterface $assetRepository
     * @param SearchCriteriaBuilder $searchCriteriaBuilder
     * @param LoggerInterface $logger
     */
    public function __construct(
        FilterBuilder $filterBuilder,
        AssetRepositoryInterface $assetRepository,
        SearchCriteriaBuilder $searchCriteriaBuilder,
        LoggerInterface $logger
    ) {
        $this->filterBuilder = $filterBuilder;
        $this->assetRepository = $assetRepository;
        $this->searchCriteriaBuilder = $searchCriteriaBuilder;
        $this->logger = $logger;
    }

    /**
     * Adds an Adobe Stock image details
     *
     * @param GetImageDetailsByAssetId $getImageDetailsByAssetId
     * @param array $imageDetails
     * @param int $assetId
     *
     * @return array
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function afterExecute(
        GetImageDetailsByAssetId $getImageDetailsByAssetId,
        array $imageDetails,
        int $assetId
    ): array {
        try {
            $mediaGalleryIdFilter = $this->filterBuilder->setField(self::MEDIA_GALLERY_ID)
                ->setValue($assetId)
                ->create();
            $searchCriteria = $this->searchCriteriaBuilder
                ->addFilter($mediaGalleryIdFilter)
                ->setPageSize(1)
                ->create();

            /** @var AssetSearchResultsInterface $result */
            $result = $this->assetRepository->getList($searchCriteria);
            if ($result->getTotalCount() > 0) {
                $item = $result->getItems();
                /** @var AssetInterface $asset */
                $asset = reset($item);
                $imageDetails['adobe_stock'] = [
                    [
                        'title' => __('ID'),
                        'value' => $asset->getId()
                    ],
                    [
                        'title' => __('Status'),
                        'value' => $asset->getIsLicensed() ? __('Licensed') : __('Unlicensed')
                    ]
                ];
            }
        } catch (Exception $exception) {
            $this->logger->critical($exception);
            $imageDetails['adobe_stock'] = [];
        }

        return $imageDetails;
    }
}
