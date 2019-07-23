<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

declare(strict_types=1);

namespace Magento\AdobeStockAsset\Model\Components;

use Magento\AdobeStockAssetApi\Api\Data\PremiumLevelSearchResultsInterface;
use Magento\AdobeStockAssetApi\Api\PremiumLevelRepositoryInterface;
use Magento\AdobeStockAssetApi\Api\Data\PremiumLevelInterface;
use Magento\Framework\Api\SearchCriteriaBuilder;
use Magento\Framework\Exception\AlreadyExistsException;

/**
 * Class SavePremiumLevelAssetComponent
 */
class SavePremiumLevelAssetComponent
{
    /**
     * @var PremiumLevelRepositoryInterface
     */
    private $premiumLevelRepository;

    /**
     * @var SearchCriteriaBuilder
     */
    private $searchCriteriaBuilder;

    /**
     * SavePremiumLevelAssetComponent constructor.
     *
     * @param PremiumLevelRepositoryInterface $premiumLevelRepository
     * @param SearchCriteriaBuilder           $searchCriteriaBuilder
     */
    public function __construct(
        PremiumLevelRepositoryInterface $premiumLevelRepository,
        SearchCriteriaBuilder $searchCriteriaBuilder
    ) {
        $this->premiumLevelRepository = $premiumLevelRepository;
        $this->searchCriteriaBuilder = $searchCriteriaBuilder;
    }

    /**
     * Create a new adobe stock asset premium level if not exists.
     *
     * @param PremiumLevelInterface $premiumLevel
     *
     * @return PremiumLevelInterface
     */
    public function execute(PremiumLevelInterface $premiumLevel): PremiumLevelInterface
    {
        try {
            $searchCriteria = $this->searchCriteriaBuilder
                ->addFilter(PremiumLevelInterface::ADOBE_ID, $premiumLevel->getAdobeId())
                ->create();

            /** @var PremiumLevelSearchResultsInterface $premiumLevelList */
            $premiumLevelList = $this->premiumLevelRepository->getList($searchCriteria);

            if (0 === $premiumLevelList->getTotalCount()) {
                $premiumLevel = $this->premiumLevelRepository->save($premiumLevel);
            } else {
                $premiumLevelItems = $premiumLevelList->getItems();
                $premiumLevel = reset($premiumLevelItems);
            }

            return $premiumLevel;
        } catch (AlreadyExistsException $exception) {
            return $premiumLevel;
        }
    }
}
