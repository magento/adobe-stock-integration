<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

declare(strict_types=1);

namespace Magento\AdobeStockImage\Model\Plugin;

use Magento\AdobeStockAssetApi\Api\AssetRepositoryInterface;
use Magento\Framework\Api\FilterBuilder;
use Magento\Framework\Api\Search\SearchCriteriaBuilder;
use Magento\MediaGalleryUi\Ui\Component\Listing\Provider;

/**
 * Class provides license data for media gallery asset
 */
class AddLicenseDataToAsset
{
    /**
     * @var AssetRepositoryInterface
     */
    private $assetRepository;

    /**
     * @var FilterBuilder
     */
    private $filterBuilder;

    /**
     * @var SearchCriteriaBuilder
     */
    private $searchCriteriaBuilder;

    /**
     * @param AssetRepositoryInterface $assetRepository
     * @param FilterBuilder $filterBuilder
     * @param SearchCriteriaBuilder $searchCriteriaBuilder
     */
    public function __construct(
        AssetRepositoryInterface $assetRepository,
        FilterBuilder $filterBuilder,
        SearchCriteriaBuilder $searchCriteriaBuilder
    ) {
        $this->assetRepository = $assetRepository;
        $this->filterBuilder = $filterBuilder;
        $this->searchCriteriaBuilder = $searchCriteriaBuilder;
    }

    /**
     * Add license data to assets.
     *
     * @param Provider $subject
     * @param array $result
     * @return array
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function afterGetData(
        Provider $subject,
        array $result
    ) : array {
        $assetIds = array_column($result, 'id');

        $mediaGalleryIdFilter = $this->filterBuilder->setField('media_gallery_id')
            ->setValue($assetIds)
            ->setConditionType('in')
            ->create();
        $searchCriteria = $this->searchCriteriaBuilder
            ->addFilter($mediaGalleryIdFilter)
            ->create();

        $stockAssets = $this->assetRepository->getList($searchCriteria);

        $licensedData = [];
        foreach ($stockAssets->getItems() as $asset) {
            $licensedData[$asset->getMediaGalleryId()] = $asset->getIsLicensed();
        }

        foreach ($result as $key => $item) {
            if (isset($licensedData[$item['id']])) {
                $result[$key]['licensed'] = $licensedData[$item['id']];
            }
        }

        return $result;
    }
}
