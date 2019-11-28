<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Magento\AdobeStockAsset\Model;

use Magento\AdobeStockAssetApi\Api\GetAssetListInterface;
use Magento\Framework\Api\FilterBuilder;
use Magento\Framework\Api\Search\Document;
use Magento\Framework\Api\Search\SearchCriteriaBuilder;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\AdobeStockAssetApi\Api\GetAssetByIdInterface;

/**
 * Service for getting asset by content id
 */
class GetAssetById implements GetAssetByIdInterface
{
    /**
     * @var SearchCriteriaBuilder
     */
    private $searchCriteriaBuilder;

    /**
     * @var GetAssetListInterface
     */
    private $getAssetList;

    /**
     * @var FilterBuilder
     */
    private $filterBuilder;

    /**
     * @param FilterBuilder $filterBuilder
     * @param GetAssetListInterface $getAssetList
     * @param SearchCriteriaBuilder $searchCriteriaBuilder
     */
    public function __construct(
        FilterBuilder $filterBuilder,
        GetAssetListInterface $getAssetList,
        SearchCriteriaBuilder $searchCriteriaBuilder
    ) {
        $this->filterBuilder = $filterBuilder;
        $this->getAssetList = $getAssetList;
        $this->searchCriteriaBuilder = $searchCriteriaBuilder;
    }

    /**
     * @inheritdoc
     */
    public function execute(int $adobeId): Document
    {
        $mediaIdFilter = $this->filterBuilder->setField('media_id')
            ->setValue($adobeId)
            ->create();
        $searchCriteria = $this->searchCriteriaBuilder
            ->addFilter($mediaIdFilter)
            ->create();

        $items = $this->getAssetList->execute($searchCriteria)->getItems();
        if (empty($items) || 1 < count($items)) {
            $message = __('Requested image doesn\'t exists');
            throw new NoSuchEntityException($message);
        }

        return reset($items);
    }
}
