<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Magento\AdobeStockAsset\Model;

use Magento\AdobeStockAsset\Model\DocumentToAsset;
use Magento\AdobeStockAssetApi\Api\Data\AssetInterface;
use Magento\AdobeStockAssetApi\Api\GetAssetListInterface;
use Magento\Framework\Api\FilterBuilder;
use Magento\Framework\Api\Search\SearchCriteriaBuilder;
use Magento\Framework\Exception\NotFoundException;

/**
 * Class GetAssetById
 *
 * Service for getting asset by content id
 */
class GetAssetById
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
     * @var DocumentToAsset
     */
    private $documentToAsset;

    /**
     * GetImageByAdobeId constructor.
     *
     * @param FilterBuilder $filterBuilder
     * @param GetAssetListInterface $getAssetList
     * @param SearchCriteriaBuilder $searchCriteriaBuilder
     * @param DocumentToAsset $documentToAsset
     */
    public function __construct(
        FilterBuilder $filterBuilder,
        GetAssetListInterface $getAssetList,
        SearchCriteriaBuilder $searchCriteriaBuilder,
        DocumentToAsset $documentToAsset
    ) {
        $this->filterBuilder = $filterBuilder;
        $this->getAssetList = $getAssetList;
        $this->searchCriteriaBuilder = $searchCriteriaBuilder;
        $this->documentToAsset = $documentToAsset;
    }

    /**
     * Returns image by Adobe ID
     *
     * @param int $adobeId
     * @return AssetInterface
     * @throws NotFoundException
     * @throws \Magento\Framework\Exception\LocalizedException
     * @throws \Magento\Payment\Gateway\Http\ConverterException
     */
    public function execute(int $adobeId): AssetInterface
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
            throw new NotFoundException($message);
        }

        return $this->documentToAsset->convert(reset($items));
    }
}
