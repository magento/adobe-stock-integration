<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Magento\AdobeStockAsset\Model;

use Magento\AdobeStockAssetApi\Api\AssetRepositoryInterface;
use Magento\AdobeStockAssetApi\Api\CategoryRepositoryInterface;
use Magento\AdobeStockAssetApi\Api\CreatorRepositoryInterface;
use Magento\AdobeStockAssetApi\Api\Data\AssetInterface;
use Magento\Framework\Exception\AlreadyExistsException;
use Magento\Framework\Exception\NoSuchEntityException;

/**
 * Class SaveAsset
 *
 * Service for saving asset
 */
class SaveAsset
{
    /**
     * @var AssetRepositoryInterface
     */
    private $assetRepository;

    /**
     * @var CategoryRepositoryInterface
     */
    private $categoryRepository;

    /**
     * @var CreatorRepositoryInterface
     */
    private $creatorRepository;

    /**
     * SaveAsset constructor.
     *
     * @param AssetRepositoryInterface $assetRepository
     * @param CreatorRepositoryInterface $creatorRepository
     * @param CategoryRepositoryInterface $categoryRepository
     */
    public function __construct(
        AssetRepositoryInterface $assetRepository,
        CreatorRepositoryInterface $creatorRepository,
        CategoryRepositoryInterface $categoryRepository
    ) {
        $this->assetRepository = $assetRepository;
        $this->creatorRepository = $creatorRepository;
        $this->categoryRepository = $categoryRepository;
    }

    /**
     * Save asset
     *
     * @param AssetInterface $asset
     * @throws AlreadyExistsException
     */
    public function execute(AssetInterface $asset): void
    {
        $category = $asset->getCategory();
        if ($category !== null) {
            $category = $this->categoryRepository->save($category);
        }
        $asset->setCategoryId($category->getId());

        $creator = $asset->getCreator();
        if ($creator !== null) {
            $creator = $this->creatorRepository->save($creator);
        }
        $asset->setCreatorId($creator->getId());

        $this->assetRepository->save($asset);
    }
}
