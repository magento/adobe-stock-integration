<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

declare(strict_types=1);

namespace Magento\AdobeStockAsset\Model\Components;

use Magento\AdobeStockAssetApi\Api\Data\MediaTypeSearchResultsInterface;
use Magento\AdobeStockAssetApi\Api\MediaTypeRepositoryInterface;
use Magento\AdobeStockAssetApi\Api\Data\MediaTypeInterface;
use Magento\Framework\Api\SearchCriteriaBuilder;
use Magento\Framework\Exception\AlreadyExistsException;

/**
 * Class SaveMediaTypeAssetComponent
 */
class SaveMediaTypeAssetComponent
{
    /**
     * @var MediaTypeRepositoryInterface
     */
    private $mediaTypeRepository;

    /**
     * @var SearchCriteriaBuilder
     */
    private $searchCriteriaBuilder;

    /**
     * SaveMediaTypeAssetComponent constructor.
     *
     * @param MediaTypeRepositoryInterface $mediaTypeRepository
     * @param SearchCriteriaBuilder        $searchCriteriaBuilder
     */
    public function __construct(
        MediaTypeRepositoryInterface $mediaTypeRepository,
        SearchCriteriaBuilder $searchCriteriaBuilder
    ) {
        $this->mediaTypeRepository = $mediaTypeRepository;
        $this->searchCriteriaBuilder = $searchCriteriaBuilder;
    }

    /**
     * Create a new adobe stock asset mediaType if not exists.
     *
     * @param MediaTypeInterface $mediaType
     *
     * @return MediaTypeInterface
     */
    public function execute(MediaTypeInterface $mediaType): MediaTypeInterface
    {
        try {
            $searchCriteria = $this->searchCriteriaBuilder
                ->addFilter(MediaTypeInterface::ADOBE_ID, $mediaType->getAdobeId())
                ->create();

            /** @var MediaTypeSearchResultsInterface $mediaTypeList */
            $mediaTypeList = $this->mediaTypeRepository->getList($searchCriteria);

            if (0 === $mediaTypeList->getTotalCount()) {
                $mediaType = $this->mediaTypeRepository->save($mediaType);
            } else {
                $mediaTypeItems = $mediaTypeList->getItems();
                $mediaType = reset($mediaTypeItems);
            }

            return $mediaType;
        } catch (AlreadyExistsException $exception) {
            return $mediaType;
        }
    }
}
