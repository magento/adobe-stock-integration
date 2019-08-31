<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Magento\AdobeStockImage\Model;

use Magento\AdobeStockAsset\Model\DocumentToAsset;
use Magento\AdobeStockAssetApi\Api\Data\AssetInterface;
use Magento\AdobeStockImageApi\Api\GetImageListInterface;
use Magento\Framework\Api\FilterBuilder;
use Magento\Framework\Api\Search\SearchCriteriaBuilder;
use Magento\Framework\Exception\NotFoundException;

/**
 * Class GetImageByAdobeId
 *
 * Service for getting image by content id
 */
class GetImageByAdobeId
{
    /**
     * @var SearchCriteriaBuilder
     */
    private $searchCriteriaBuilder;

    /**
     * @var GetImageListInterface
     */
    private $getImageList;

    /**
     * @var FilterBuilder
     */
    private $filterBuilder;

    /**
     * @var DocumentToAsset
     */
    private $documentToAsset;

    public function __construct(
        FilterBuilder $filterBuilder,
        GetImageListInterface $getImageList,
        SearchCriteriaBuilder $searchCriteriaBuilder,
        DocumentToAsset $documentToAsset
    ) {
        $this->filterBuilder = $filterBuilder;
        $this->getImageList = $getImageList;
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

        $items = $this->getImageList->execute($searchCriteria)->getItems();
        if (empty($items) || 1 < count($items)) {
            $message = __('Requested image doesn\'t exists');
            throw new NotFoundException($message);
        }

        return $this->documentToAsset->convert(reset($items));
    }
}
