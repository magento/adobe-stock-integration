<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

declare(strict_types=1);

namespace Magento\AdobeStockImage\Model;

use Magento\AdobeStockAsset\Model\SavePreviewImageAssetStrategy;
use Magento\AdobeStockAssetApi\Api\AssetRepositoryInterface;
use Magento\AdobeStockAssetApi\Api\CategoryRepositoryInterface;
use Magento\AdobeStockAssetApi\Api\CreatorRepositoryInterface;
use Magento\AdobeStockAssetApi\Api\Data\AssetInterface;
use Magento\AdobeStockAssetApi\Api\Data\CategoryInterface;
use Magento\AdobeStockAssetApi\Api\Data\CreatorInterface;
use Magento\AdobeStockImageApi\Api\GetImageListInterface;
use Magento\AdobeStockImageApi\Api\SaveImagePreviewInterface;
use Magento\Framework\Exception\AlreadyExistsException;
use Magento\Framework\Exception\CouldNotSaveException;
use Magento\AdobeStockAssetApi\Api\Data\AssetSearchResultsInterface;
use Magento\Framework\Exception\IntegrationException;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Exception\NotFoundException;
use Psr\Log\LoggerInterface;
use Magento\Framework\Api\SearchCriteriaBuilder;

/**
 * Class SaveImagePreview
 */
class SaveImagePreview implements SaveImagePreviewInterface
{
    /**
     * @var AssetRepositoryInterface
     */
    private $assetRepository;

    /**
     * @var Storage
     */
    private $storage;

    /**
     * @var LoggerInterface
     */
    private $logger;

    /**
     * @var GetImageListInterface
     */
    private $getImageList;

    /**
     * @var SearchCriteriaBuilder
     */
    private $searchCriteriaBuilder;

    /**
     * @var CreatorRepositoryInterface
     */
    private $creatorRepository;

    /**
     * @var CategoryRepositoryInterface
     */
    private $categoryRepository;

    /**
     * SaveImagePreview constructor.
     * @param AssetRepositoryInterface $assetRepository
     * @param Storage $storage
     * @param LoggerInterface $logger
     * @param GetImageListInterface $getImageList
     * @param SearchCriteriaBuilder $searchCriteriaBuilder
     */
    public function __construct(
        AssetRepositoryInterface $assetRepository,
        CreatorRepositoryInterface $creatorRepository,
        CategoryRepositoryInterface $categoryRepository,
        Storage $storage,
        LoggerInterface $logger,
        GetImageListInterface $getImageList,
        SearchCriteriaBuilder $searchCriteriaBuilder
    ) {
        $this->assetRepository = $assetRepository;
        $this->creatorRepository = $creatorRepository;
        $this->categoryRepository = $categoryRepository;
        $this->storage = $storage;
        $this->logger = $logger;
        $this->getImageList = $getImageList;
        $this->searchCriteriaBuilder = $searchCriteriaBuilder;
    }

    /**
     * @inheritdoc
     */
    public function execute(int $adobeId, string $destinationPath): void
    {
        $searchResult = $this->getImagesByAdobeId($adobeId);

        if (1 < $searchResult->getTotalCount()) {
            $message = __('Requested image doesn\'t exists');
            $this->logger->critical($message);
            throw new NotFoundException($message);
        }

        try {
            $items = $searchResult->getItems();
            $asset = reset($items);
            $path = $this->storage->save($asset->getPreviewUrl(), $destinationPath);
            $asset->setPath($path);
            $asset->isObjectNew(true);
            $this->saveAsset($asset);
        } catch (\Exception $exception) {
            $message = __('Image was not saved: %1', $exception->getMessage());
            $this->logger->critical($message);
            throw new CouldNotSaveException($message);
        }
    }

    /**
     * @param AssetInterface $asset
     * @throws AlreadyExistsException
     */
    private function saveAsset(AssetInterface $asset)
    {
        $category = $this->saveCategory($asset->getCategory());
        $creator = $this->saveCreator($asset->getCreator());
        $asset->setCategoryId($category->getId());
        $asset->setCreatorId($creator->getId());
        $this->assetRepository->save($asset);
    }

    /**
     * @param CategoryInterface $category
     * @return CategoryInterface|mixed
     */
    private function saveCategory(CategoryInterface $category)
    {
        try {
            $searchCriteria = $this->searchCriteriaBuilder
                ->addFilter(CategoryInterface::ADOBE_ID, $category->getAdobeId())
                ->create();

            $categoryList = $this->categoryRepository->getList($searchCriteria);
            if (0 === $categoryList->getTotalCount()) {
                $category = $this->categoryRepository->save($category);
            } else {
                $categoryItems = $categoryList->getItems();
                $category = reset($categoryItems);
            }
            return $category;
        } catch (AlreadyExistsException $exception) {
            return $category;
        }
    }

    /**
     * @param CreatorInterface $creator
     * @return CreatorInterface|mixed
     */
    private function saveCreator(CreatorInterface $creator)
    {
        try {
            $searchCriteria = $this->searchCriteriaBuilder
                ->addFilter(CreatorInterface::ADOBE_ID, $creator->getAdobeId())
                ->create();

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

    /**
     * @param int $adobeId
     * @return AssetSearchResultsInterface
     * @throws LocalizedException
     */
    private function getImagesByAdobeId(int $adobeId): AssetSearchResultsInterface
    {
        $searchCriteria = $this->searchCriteriaBuilder
            ->addFilter('media_id', $adobeId)
            ->setSortOrders([])
            ->create();

        return $this->getImageList->execute($searchCriteria);
    }
}
