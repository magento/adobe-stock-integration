<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

declare(strict_types=1);

namespace Magento\AdobeStockImage\Model;

use Magento\AdobeStockAsset\Model\DocumentToAsset;
use Magento\AdobeStockAssetApi\Api\AssetRepositoryInterface;
use Magento\AdobeStockAssetApi\Api\CategoryRepositoryInterface;
use Magento\AdobeStockAssetApi\Api\CreatorRepositoryInterface;
use Magento\AdobeStockAssetApi\Api\Data\AssetInterface;
use Magento\AdobeStockImageApi\Api\GetImageListInterface;
use Magento\AdobeStockImageApi\Api\SaveImagePreviewInterface;
use Magento\Framework\Api\FilterBuilder;
use Magento\Framework\Api\Search\SearchCriteriaBuilder;
use Magento\Framework\Exception\AlreadyExistsException;
use Magento\Framework\Exception\CouldNotSaveException;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Exception\NotFoundException;
use Psr\Log\LoggerInterface;

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
     * @var DocumentToAsset
     */
    private $documentToAsset;

    /**
     * @var FilterBuilder
     */
    private $filterBuilder;

    /**
     * SaveImagePreview constructor.
     * @param AssetRepositoryInterface $assetRepository
     * @param CreatorRepositoryInterface $creatorRepository
     * @param CategoryRepositoryInterface $categoryRepository
     * @param Storage $storage
     * @param LoggerInterface $logger
     * @param GetImageListInterface $getImageList
     * @param SearchCriteriaBuilder $searchCriteriaBuilder
     * @param DocumentToAsset $documentToAsset
     * @param FilterBuilder $filterBuilder
     */
    public function __construct(
        AssetRepositoryInterface $assetRepository,
        CreatorRepositoryInterface $creatorRepository,
        CategoryRepositoryInterface $categoryRepository,
        Storage $storage,
        LoggerInterface $logger,
        GetImageListInterface $getImageList,
        SearchCriteriaBuilder $searchCriteriaBuilder,
        DocumentToAsset $documentToAsset,
        FilterBuilder $filterBuilder
    ) {
        $this->assetRepository = $assetRepository;
        $this->creatorRepository = $creatorRepository;
        $this->categoryRepository = $categoryRepository;
        $this->storage = $storage;
        $this->logger = $logger;
        $this->getImageList = $getImageList;
        $this->searchCriteriaBuilder = $searchCriteriaBuilder;
        $this->documentToAsset = $documentToAsset;
        $this->filterBuilder = $filterBuilder;
    }

    /**
     * @inheritdoc
     */
    public function execute(int $adobeId, string $destinationPath): void
    {
        try {
            $asset = $this->getImageByAdobeId($adobeId);
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
     * @return AssetInterface
     * @throws LocalizedException
     */
    private function getImageByAdobeId(int $adobeId): AssetInterface
    {
        $mediaIdFilter = $this->filterBuilder->setField('media_id')
            ->setValue($adobeId)
            ->create();
        $searchCriteria = $this->searchCriteriaBuilder
            ->addFilter($mediaIdFilter)
            ->create();

        $items = $this->getImageList->execute($searchCriteria)->getItems();
        if (empty($items) || 1 < count($items)) {
            $message = __('Requested image doesn\'t exists');
            throw new NotFoundException($message);
        }

        return $this->documentToAsset->convert(reset($items));
    }
}
