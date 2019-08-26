<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

declare(strict_types=1);

namespace Magento\AdobeStockImage\Model;

use Magento\AdobeStockImageApi\Api\GetImageListInterface;
use Magento\Framework\Api\Search\Document;
use Magento\Framework\Api\SearchCriteriaBuilder;
use Magento\Framework\Exception\IntegrationException;
use Magento\Framework\Exception\SerializationException;
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
     * GetImageSeries constructor.
     *
     * @param GetImageListInterface $getImageList
     * @param SearchCriteriaBuilder $searchCriteriaBuilder
     * @param LoggerInterface       $logger
     */
    public function __construct(
        GetImageListInterface $getImageList,
        SearchCriteriaBuilder $searchCriteriaBuilder,
        LoggerInterface $logger
    ) {
        $this->getImageList = $getImageList;
        $this->searchCriteriaBuilder = $searchCriteriaBuilder;
        $this->logger = $logger;
    }

    /**
     * Get image related image series.
     *
     * @param int $serieId
     *
     * @return array
     * @throws IntegrationException
     */
    public function execute(int $serieId): array
    {
        try {
            $searchCriteria = $this->searchCriteriaBuilder->addFilter('serie_id', $serieId)
                ->setSortOrders([])
                ->create();
            $items = $this->getImageList->execute($searchCriteria)->getItems();
            $series = $this->serializeImageSeries($items);

            return $series;
        } catch (\Exception $exception) {
            $message = __('Get image series list failed: %s', $exception->getMessage());
            throw new IntegrationException($message, $exception);
        }
    }

    /**
     * Serialize image series data.
     *
     * @param Document[] $series
     *
     * @return array
     * @throws SerializationException
     */
    private function serializeImageSeries(array $series): array
    {
        $data = [];
        try {
            /** @var Document $seriesItem */
            foreach ($series as $seriesItem) {
                $item['id'] = $seriesItem->getId();
                $item['title'] = $seriesItem->getCustomAttribute('title')->getValue();
                $item['thumbnail_url'] = $seriesItem->getCustomAttribute('thumbnail_240_url')->getValue();
                $data[] = $item;
            }

            $result = [
                'type' => 'series',
                'series' => $data,
            ];

            return $result;
        } catch (\Exception $exception) {
            $message = __('An error occurred during image series serialization: %s', $exception->getMessage());
            throw new SerializationException($message);
        }
    }
}
