<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

declare(strict_types=1);

namespace Magento\AdobeStockAsset\Model;

use Magento\AdobeStockAsset\Model\Components\SaveCategoryAssetComponent;
use Magento\AdobeStockAsset\Model\Components\SaveCreatorAssetComponent;
use Magento\AdobeStockAssetApi\Api\AssetRepositoryInterface;
use Magento\AdobeStockAssetApi\Api\Data\AssetInterface;
use Magento\Framework\Exception\AlreadyExistsException;

/**
 * Class SavePreviewImageAssetStrategy
 */
class SavePreviewImageAssetStrategy
{
    /**
     * @var SaveCategoryAssetComponent
     */
    private $saveCategoryAssetComponent;

    /**
     * @var SaveCreatorAssetComponent
     */
    private $saveCreatorAssetComponent;

    /**
     * @var AssetRepositoryInterface
     */
    private $assetRepository;

    /**
     * SavePreviewImageAssetStrategy constructor.
     *
     * @param SaveCategoryAssetComponent     $saveCategoryAssetComponent
     * @param SaveCreatorAssetComponent      $saveCreatorAssetComponent
     * @param AssetRepositoryInterface       $assetRepository
     */
    public function __construct(
        SaveCategoryAssetComponent $saveCategoryAssetComponent,
        SaveCreatorAssetComponent $saveCreatorAssetComponent,
        AssetRepositoryInterface $assetRepository
    ) {
        $this->saveCategoryAssetComponent = $saveCategoryAssetComponent;
        $this->saveCreatorAssetComponent = $saveCreatorAssetComponent;
        $this->assetRepository = $assetRepository;
    }

    /**
     * Execute save asset process. At first save asset components data, then set related info to the asset object.
     *
     * @param AssetInterface $asset
     *
     * @return AssetInterface
     */
    public function execute(AssetInterface $asset): AssetInterface
    {
        try {
            $categoryAssetComponent = $this->saveCategoryAssetComponent->execute($asset->getCategory());
            $creatorAssetComponent = $this->saveCreatorAssetComponent->execute($asset->getCreator());

            $asset->setAdobeId($asset->getId());
            $asset->setId(null);
            $asset->setData(AssetInterface::CATEGORY_ID, $categoryAssetComponent->getId());
            $asset->setData(AssetInterface::CREATOR_ID, $creatorAssetComponent->getId());
            $this->assetRepository->save($asset);

            return $asset;
        } catch (AlreadyExistsException $exception) {
            return $asset;
        }
    }
}
