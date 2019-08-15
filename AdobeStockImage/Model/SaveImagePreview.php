<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

declare(strict_types=1);

namespace Magento\AdobeStockImage\Model;

use Magento\AdobeStockAssetApi\Api\AssetRepositoryInterface;
use Magento\AdobeStockAssetApi\Api\CategoryRepositoryInterface;
use Magento\AdobeStockAssetApi\Api\CreatorRepositoryInterface;
use Magento\AdobeStockAssetApi\Api\Data\AssetInterface;
use Magento\AdobeStockImageApi\Api\GetImageListInterface;
use Magento\AdobeStockImageApi\Api\SaveImagePreviewInterface;
use Magento\Framework\Exception\AlreadyExistsException;
use Magento\Framework\Exception\CouldNotSaveException;
use Magento\AdobeStockAssetApi\Api\Data\AssetSearchResultsInterface;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Exception\NoSuchEntityException;
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
     *
     * @param AssetRepositoryInterface    $assetRepository
     * @param CreatorRepositoryInterface  $creatorRepository
     * @param CategoryRepositoryInterface $categoryRepository
     * @param Storage                     $storage
     * @param LoggerInterface             $logger
     * @param GetImageListInterface       $getImageList
     * @param SearchCriteriaBuilder       $searchCriteriaBuilder
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
            $this->saveAsset($asset);
        } catch (\Exception $exception) {
            $message = __('Image was not saved: %1', $exception->getMessage());
            $this->logger->critical($message);
            throw new CouldNotSaveException($message);
        }
    }

    /**
     * Save asset.
     *
     * @param AssetInterface $asset
     * @throws AlreadyExistsException
     */
    private function saveAsset(AssetInterface $asset): void
    {
        if (!$this->isAssetSaved($asset->getId())) {
            $asset->isObjectNew(true);
        }
        $category = $asset->getCategory();
        if ($category !== null && !$this->isCategorySaved($category->getId())) {
            $category->isObjectNew(true);
            $category = $this->categoryRepository->save($category);
        }
        $asset->setCategoryId($category->getId());

        $creator = $asset->getCreator();
        if ($creator !== null && !$this->isCreatorSaved($creator->getId())) {
            $creator->isObjectNew(true);
            $creator = $this->creatorRepository->save($creator);
        }
        $asset->setCreatorId($creator->getId());

        $this->assetRepository->save($asset);
    }

    /**
     * Is asset already exists.
     *
     * @param int $id
     * @return bool
     */
    private function isAssetSaved(int $id): bool
    {
        try {
            $asset = $this->assetRepository->getById($id);
            return $asset->getId() !== null;
        } catch (NoSuchEntityException $exception) {
            return false;
        }
    }

    /**
     * Is asset category exists.
     *
     * @param int $id
     * @return bool
     */
    private function isCategorySaved(int $id): bool
    {
        try {
            $category = $this->categoryRepository->getById($id);
            return $category->getId() !== null;
        } catch (NoSuchEntityException $exception) {
            return false;
        }
    }

    /**
     * Is creator already exists.
     *
     * @param int $id
     * @return bool
     */
    private function isCreatorSaved(int $id): bool
    {
        try {
            $creator = $this->creatorRepository->getById($id);
            return $creator->getId() !== null;
        } catch (NoSuchEntityException $exception) {
            return false;
        }
    }

    /**
     * Get image by adobe id.
     *
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
