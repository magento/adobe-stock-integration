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
use Magento\Framework\Api\SearchCriteriaBuilder;
use Magento\Framework\Exception\IntegrationException;
use Psr\Log\LoggerInterface;

/**
 * Class GetImageSeries
 */
class GetImageSeries
{
    /**
     * @var GetImageListInterface
     */
    private $getImageList;

    /**
     * @var SearchCriteriaBuilder
     */
    private $searchCriteriaBuilder;

    /**
     * @var LoggerInterface
     */
    private $logger;

    /**
     * @var DocumentToAsset
     */
    private $documentToAsset;

    /**
     * GetImageSerie constructor.
     *
     * @param GetImageListInterface $getImageList
     * @param SearchCriteriaBuilder $searchCriteriaBuilder
     * @param DocumentToAsset       $documentToAsset
     * @param LoggerInterface       $logger
     */
    public function __construct(
        GetImageListInterface $getImageList,
        SearchCriteriaBuilder $searchCriteriaBuilder,
        DocumentToAsset $documentToAsset,
        LoggerInterface $logger
    ) {
        $this->getImageList = $getImageList;
        $this->searchCriteriaBuilder = $searchCriteriaBuilder;
        $this->logger = $logger;
        $this->documentToAsset = $documentToAsset;
    }

    /**
     * Get image serie.
     *
     * @param int $serieId
     *
     * @return AssetInterface[]
     * @throws IntegrationException
     */
    public function execute(int $serieId): array
    {
        try {
            $searchCriteria = $this->searchCriteriaBuilder->addFilter('serie_id', $serieId)
                ->setSortOrders([])
                ->create();
            $items = $this->getImageList->execute($searchCriteria)->getItems();

            $assets = [];
            foreach ($items as $item) {
                $assets[] = $this->documentToAsset->convert($item);
            }

            return $assets;
        } catch (\Exception $exception) {
            $message = __('Get image series list failed: %s', $exception->getMessage());
            throw new IntegrationException($message, $exception);
        }
    }
}
