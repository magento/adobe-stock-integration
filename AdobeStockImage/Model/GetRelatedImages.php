<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

declare(strict_types=1);

namespace Magento\AdobeStockImage\Model;

use Magento\AdobeStockImageApi\Api\GetImageListInterface;
use Magento\AdobeStockImageApi\Api\GetRelatedImagesInterface;
use Magento\Framework\Api\Search\SearchCriteriaBuilder;
use Magento\Framework\Api\FilterBuilder;
use Magento\Framework\Exception\IntegrationException;
use Psr\Log\LoggerInterface;

/**
 * Class GetRelatedImages
 */
class GetRelatedImages implements GetRelatedImagesInterface
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
     * @var FilterBuilder
     */
    private $filterBuilder;

    /**
     * @var LoggerInterface
     */
    private $logger;

    /**
     * @var string[]
     */
    private $fields;

    /**
     * @var SerializeImageAsset
     */
    private $serializeImageAsset;

    /**
     * GetRelatedImages constructor.
     *
     * @param GetImageListInterface $getImageList
     * @param SearchCriteriaBuilder $searchCriteriaBuilder
     * @param FilterBuilder $filterBuilder
     * @param SerializeImageAsset $serializeImageAsset
     * @param LoggerInterface $logger
     * @param array $fields
     */
    public function __construct(
        GetImageListInterface $getImageList,
        SearchCriteriaBuilder $searchCriteriaBuilder,
        FilterBuilder $filterBuilder,
        SerializeImageAsset $serializeImageAsset,
        LoggerInterface $logger,
        array $fields = []
    ) {
        $this->getImageList = $getImageList;
        $this->searchCriteriaBuilder = $searchCriteriaBuilder;
        $this->filterBuilder = $filterBuilder;
        $this->serializeImageAsset = $serializeImageAsset;
        $this->logger = $logger;
        $this->fields = $fields;
    }

    /**
     * Retrieve array of related images categorized by relation.
     *
     * @param int $imageId
     * @param int $limit
     *
     * @return array
     * @throws IntegrationException
     */
    public function execute(int $imageId, int $limit): array
    {
        $relatedImageGroups = [];
        try {
            foreach ($this->fields as $key => $field) {
                $filter = $this->filterBuilder->setField($field)->setValue($imageId)->create();
                $searchCriteria = $this->searchCriteriaBuilder->addFilter($filter)
                    ->setPageSize($limit)
                    ->create();
                $relatedImageGroups[$key] = $this->serializeImageAsset->execute(
                    $this->getImageList->execute($searchCriteria)->getItems()
                );
            }
            return $relatedImageGroups;
        } catch (\Exception $exception) {
            $message = __('Get related images list failed: %error', ['error' => $exception->getMessage()]);
            $this->logger->critical($exception);
            throw new IntegrationException($message, $exception);
        }
    }
}
