<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

declare(strict_types=1);

namespace Magento\AdobeStockAsset\Model\Components;

use Magento\AdobeStockAssetApi\Api\CreatorRepositoryInterface;
use Magento\AdobeStockAssetApi\Api\Data\CategorySearchResultsInterface;
use Magento\AdobeStockAssetApi\Api\Data\CreatorInterface;
use Magento\Framework\Api\SearchCriteriaBuilder;
use Magento\Framework\Exception\AlreadyExistsException;

/**
 * Class SaveCreatorAssetComponent
 */
class SaveCreatorAssetComponent
{
    /**
     * @var CreatorRepositoryInterface
     */
    private $creatorRepository;

    /**
     * @var SearchCriteriaBuilder
     */
    private $searchCriteriaBuilder;

    /**
     * SaveCreatorAssetComponent constructor.
     *
     * @param CreatorRepositoryInterface $creatorRepository
     * @param SearchCriteriaBuilder      $searchCriteriaBuilder
     */
    public function __construct(
        CreatorRepositoryInterface $creatorRepository,
        SearchCriteriaBuilder $searchCriteriaBuilder
    ) {
        $this->creatorRepository = $creatorRepository;
        $this->searchCriteriaBuilder = $searchCriteriaBuilder;
    }

    /**
     * Create a new adobe stock asset creator if not exists.
     *
     * @param CreatorInterface $creator
     *
     * @return CreatorInterface
     */
    public function execute(CreatorInterface $creator): CreatorInterface
    {
        try {
            $searchCriteria = $this->searchCriteriaBuilder
                ->addFilter(CreatorInterface::ADOBE_ID, $creator->getAdobeId())
                ->create();

            /** @var CategorySearchResultsInterface $creatorList */
            $creatorList = $this->creatorRepository->getList($searchCriteria);

            if (0 === $creatorList->getTotalCount()) {
                $creator = $this->creatorRepository->save($creator);
            } else {
                $creatorListItems = $creatorList->getItems();
                $creator = reset($creatorListItems);
            }

            return $creator;
        } catch (AlreadyExistsException $exception) {
            return $creator;
        }
    }
}
