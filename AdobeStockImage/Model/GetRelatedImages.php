<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

declare(strict_types=1);

namespace Magento\AdobeStockImage\Model;

use Magento\AdobeStockImageApi\Api\GetImageListInterface;
use Magento\AdobeStockImageApi\Api\GetRelatedImagesInterface;
use Magento\Framework\Api\FilterBuilder;
use Magento\Framework\Api\Search\Document;
use Magento\Framework\Api\Search\SearchCriteriaBuilder;
use Magento\Framework\Exception\AuthenticationException;
use Magento\Framework\Exception\LocalizedException;
use Psr\Log\LoggerInterface;

/**
 * Get list of related images by the image id.
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
     * @var SerializeImage
     */
    private $serializeImage;

    /**
     * @param GetImageListInterface $getImageList
     * @param SearchCriteriaBuilder $searchCriteriaBuilder
     * @param FilterBuilder $filterBuilder
     * @param SerializeImage $serializeImage
     * @param LoggerInterface $logger
     * @param array $fields
     */
    public function __construct(
        GetImageListInterface $getImageList,
        SearchCriteriaBuilder $searchCriteriaBuilder,
        FilterBuilder $filterBuilder,
        SerializeImage $serializeImage,
        LoggerInterface $logger,
        array $fields = []
    ) {
        $this->getImageList = $getImageList;
        $this->searchCriteriaBuilder = $searchCriteriaBuilder;
        $this->filterBuilder = $filterBuilder;
        $this->serializeImage = $serializeImage;
        $this->logger = $logger;
        $this->fields = $fields;
    }

    /**
     * @inheritdoc
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
                $relatedImageGroups[$key] = $this->serializeRelatedImages(
                    $this->getImageList->execute($searchCriteria)->getItems()
                );
            }
            return $relatedImageGroups;
        } catch (AuthenticationException $exception) {
            throw $exception;
        } catch (\Exception $exception) {
            $this->logger->critical($exception);
            throw new LocalizedException(
                __(
                    'Could not load related assets for asset id: %id',
                    [
                        'id' => $imageId
                    ]
                ),
                $exception
            );
        }
    }

    /**
     * Serialize related image data.
     *
     * @param Document[] $images
     * @return array
     */
    private function serializeRelatedImages(array $images): array
    {
        $data = [];
        foreach ($images as $image) {
            $data[] = $this->serializeImage->execute($image);
        }
        return $data;
    }
}
