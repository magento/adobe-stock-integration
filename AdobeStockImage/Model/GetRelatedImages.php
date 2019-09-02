<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

declare(strict_types=1);

namespace Magento\AdobeStockImage\Model;

use Magento\AdobeStockImageApi\Api\GetImageListInterface;
use Magento\Framework\Api\Search\Document;
use Magento\Framework\Api\Search\SearchCriteriaBuilder;
use Magento\Framework\Api\FilterBuilder;
use Magento\Framework\Exception\IntegrationException;
use Magento\Framework\Exception\SerializationException;
use Psr\Log\LoggerInterface;

/**
 * Class GetRelatedImages
 */
class GetRelatedImages
{
    /*
     * Series ID.
     */
    const SERIE_ID = 'serie_id';

    /*
     * Model ID.
     */
    const MODEL_ID = 'model_id';

    /**
     * @var GetImageListInterface
     */
    private $getImageList;

    /**
     * @var SearchCriteriaBuilder
     */
    private $searchCriteriaBuilder;

    /**
     * @var FilterBuilder
     */
    private $filterBuilder;

    /**
     * @var LoggerInterface
     */
    private $logger;

    /**
     * GetRelatedImages constructor.
     * @param GetImageListInterface $getImageList
     * @param SearchCriteriaBuilder $searchCriteriaBuilder
     * @param FilterBuilder $filterBuilder
     * @param LoggerInterface $logger
     */
    public function __construct(
        GetImageListInterface $getImageList,
        SearchCriteriaBuilder $searchCriteriaBuilder,
        FilterBuilder $filterBuilder,
        LoggerInterface $logger
    ) {
        $this->getImageList = $getImageList;
        $this->searchCriteriaBuilder = $searchCriteriaBuilder;
        $this->filterBuilder = $filterBuilder;
        $this->logger = $logger;
    }

    /**
     * Get image related image series.
     *
     * @param int $imageId
     * @param int $limit
     *
     * @return array
     * @throws IntegrationException
     */
    public function execute(int $imageId, int $limit): array
    {
        try {
            $seriesFilter = $this->filterBuilder->setField(self::SERIE_ID)->setValue($imageId)->create();
            $modelFilter = $this->filterBuilder->setField(self::MODEL_ID)->setValue($imageId)->create();
            $serieSearchCriteria = $this->searchCriteriaBuilder
                ->addFilter($seriesFilter)
                ->setPageSize($limit)
                ->create();
            $modelSearchCriteria = $this->searchCriteriaBuilder
                ->addFilter($modelFilter)
                ->setPageSize($limit)
                ->create();

            return $this->serializeRelatedImages(
                $this->getImageList->execute($serieSearchCriteria)->getItems(),
                $this->getImageList->execute($modelSearchCriteria)->getItems()
            );
        } catch (\Exception $exception) {
            $message = __('Get related images list failed: %s', $exception->getMessage());
            throw new IntegrationException($message, $exception);
        }
    }

    /**
     * Serialize related image data.
     *
     * @param Document[] $series
     * @param Document[] $models
     * @return array
     * @throws SerializationException
     */
    private function serializeRelatedImages(array $series, array $models): array
    {
        $seriesData = [];
        $modelData = [];
        try {
            /** @var Document $seriesItem */
            foreach ($series as $seriesItem) {
                $item['id'] = $seriesItem->getId();
                $item['title'] = $seriesItem->getCustomAttribute('title')->getValue();
                $item['thumbnail_url'] = $seriesItem->getCustomAttribute('thumbnail_240_url')->getValue();
                $seriesData[] = $item;
            }
            /** @var Document $modelItem */
            foreach ($models as $modelItem) {
                $item['id'] = $modelItem->getId();
                $item['title'] = $modelItem->getCustomAttribute('title')->getValue();
                $item['thumbnail_url'] = $modelItem->getCustomAttribute('thumbnail_240_url')->getValue();
                $modelData[] = $item;
            }

            $result = [
                'series' => $seriesData,
                'model' => $modelData
            ];

            return $result;
        } catch (\Exception $exception) {
            $message = __('An error occurred during related images serialization: %s', $exception->getMessage());
            throw new SerializationException($message);
        }
    }
}
