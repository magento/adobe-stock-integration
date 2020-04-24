<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Magento\AdobeStockImageAdminUi\Plugin;

use Exception;
use Magento\AdobeStockAssetApi\Api\AssetRepositoryInterface;
use Magento\AdobeStockAssetApi\Api\CategoryRepositoryInterface;
use Magento\AdobeStockAssetApi\Api\CreatorRepositoryInterface;
use Magento\AdobeStockAssetApi\Api\Data\AssetInterface;
use Magento\AdobeStockAssetApi\Api\Data\AssetSearchResultsInterface;
use Magento\AdobeStockAssetApi\Api\Data\CategoryInterface;
use Magento\AdobeStockAssetApi\Api\Data\CreatorInterface;
use Magento\Framework\Api\FilterBuilder;
use Magento\Framework\Api\Search\SearchCriteriaBuilder;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\MediaGalleryUi\Model\GetDetailsByAssetId;
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
     * @var CategoryRepositoryInterface
     */
    private $categoryRepository;

    /**
     * @var CreatorRepositoryInterface
     */
    private $creatorRepository;

    /**
     * @var FilterBuilder
     */
    private $filterBuilder;

    /**
     * @var LoggerInterface
     */
    private $logger;

    /**
     * @param FilterBuilder $filterBuilder
     * @param AssetRepositoryInterface $assetRepository
     * @param CategoryRepositoryInterface $categoryRepository
     * @param CreatorRepositoryInterface $creatorRepository
     * @param SearchCriteriaBuilder $searchCriteriaBuilder
     * @param LoggerInterface $logger
     */
    public function __construct(
        FilterBuilder $filterBuilder,
        AssetRepositoryInterface $assetRepository,
        CategoryRepositoryInterface $categoryRepository,
        CreatorRepositoryInterface $creatorRepository,
        SearchCriteriaBuilder $searchCriteriaBuilder,
        LoggerInterface $logger
    ) {
        $this->filterBuilder = $filterBuilder;
        $this->assetRepository = $assetRepository;
        $this->categoryRepository = $categoryRepository;
        $this->creatorRepository = $creatorRepository;
        $this->searchCriteriaBuilder = $searchCriteriaBuilder;
        $this->logger = $logger;
    }

    /**
     * Adds an Adobe Stock image details
     *
     * @param GetDetailsByAssetId $getImageDetailsByAssetId
     * @param array $imageDetails
     * @param int $assetId
     * @return array
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function afterExecute(
        GetDetailsByAssetId $getImageDetailsByAssetId,
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
            $adobeStockInfo = [];
            if ($result->getTotalCount() > 0) {
                $item = $result->getItems();
                /** @var AssetInterface $asset */
                $asset = reset($item);
                $adobeStockInfo = $this->loadAssetsInfo($asset);
            }
            $imageDetails['adobe_stock'] = $adobeStockInfo;
        } catch (Exception $exception) {
            $this->logger->critical($exception);
            $imageDetails['adobe_stock'] = [];
        }

        return $imageDetails;
    }

    /**
     * Get an Adobe Stock asset info details.
     *
     * @param AssetInterface $asset
     *
     * @return array
     * @throws NoSuchEntityException
     */
    private function loadAssetsInfo(AssetInterface $asset): array
    {
        /** @var CategoryInterface $assetCategory */
        $assetCategory = $this->categoryRepository->getById($asset->getCategoryId());
        /** @var CreatorInterface $assetCreator */
        $assetCreator = $this->creatorRepository->getById($asset->getCreatorId());

        return [
            [
                'title' => __('ID'),
                'value' => $asset->getId(),
            ],
            [
                'title' => __('Status'),
                'value' => $asset->getIsLicensed() !== 0 ? __('Licensed') : __('Unlicensed'),
            ],
            [
                'title' => __('Category'),
                'value' => $assetCategory->getName(),
            ],
            [
                'title' => __('Author'),
                'value' => $assetCreator->getName(),
            ],
        ];
    }
}
