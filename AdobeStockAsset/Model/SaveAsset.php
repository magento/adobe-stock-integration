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
use Magento\AdobeStockAssetApi\Api\Data\AssetInterfaceFactory;
use Magento\AdobeStockAssetApi\Api\SaveAssetInterface;
use Magento\Framework\Reflection\DataObjectProcessor;

/**
 * Service for saving asset object
 */
class SaveAsset implements SaveAssetInterface
{
    private const CATEGORY = 'category';
    private const CATEGORY_ID = 'category_id';
    private const CREATOR = 'creator';
    private const CREATOR_ID = 'creator_id';

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
     * @var DataObjectProcessor
     */
    private $objectProcessor;

    /**
     * @var AssetInterfaceFactory
     */
    private $assetFactory;

    /**
     * @param AssetInterfaceFactory $assetFactory
     * @param AssetRepositoryInterface $assetRepository
     * @param CreatorRepositoryInterface $creatorRepository
     * @param CategoryRepositoryInterface $categoryRepository
     * @param DataObjectProcessor $objectProcessor
     */
    public function __construct(
        AssetInterfaceFactory $assetFactory,
        AssetRepositoryInterface $assetRepository,
        CreatorRepositoryInterface $creatorRepository,
        CategoryRepositoryInterface $categoryRepository,
        DataObjectProcessor $objectProcessor
    ) {
        $this->assetFactory = $assetFactory;
        $this->assetRepository = $assetRepository;
        $this->creatorRepository = $creatorRepository;
        $this->categoryRepository = $categoryRepository;
        $this->objectProcessor = $objectProcessor;
    }

    /**
     * @inheritdoc
     */
    public function execute(AssetInterface $asset): void
    {
        $data = $this->objectProcessor->buildOutputDataArray($asset, AssetInterface::class);

        $category = $asset->getCategory();
        if ($category !== null) {
            $categoryId = $category->getId();
            if ($categoryId !== null) {
                $category = $this->categoryRepository->save($category);
            }
            $data[self::CATEGORY_ID] = $categoryId;
            $data[self::CATEGORY] = $category;
        }
        $creator = $asset->getCreator();
        if ($creator !== null) {
            $creator = $this->creatorRepository->save($creator);
        }
        $data[self::CREATOR_ID] = $creator->getId();
        $data[self::CREATOR] = $creator;

        $this->assetRepository->save($this->assetFactory->create(['data' => $data]));
    }
}
