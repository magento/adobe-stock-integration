<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

declare(strict_types=1);

namespace Magento\AdobeStockAsset\Model;

use Magento\AdobeStockAsset\Model\Components\SaveCategoryAssetComponent;
use Magento\AdobeStockAsset\Model\Components\SaveCreatorAssetComponent;
use Magento\AdobeStockAsset\Model\Components\SaveMediaTypeAssetComponent;
use Magento\AdobeStockAsset\Model\Components\SavePremiumLevelAssetComponent;
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
     * @var SaveMediaTypeAssetComponent
     */
    private $saveMediaTypeAssetComponent;

    /**
     * @var SavePremiumLevelAssetComponent
     */
    private $savePremiumLevelAssetComponent;

    /**
     * @var AssetRepositoryInterface
     */
    private $assetRepository;

    /**
     * SavePreviewImageAssetStrategy constructor.
     *
     * @param SaveCategoryAssetComponent     $saveCategoryAssetComponent
     * @param SaveCreatorAssetComponent      $saveCreatorAssetComponent
     * @param SaveMediaTypeAssetComponent    $saveMediaTypeAssetComponent
     * @param SavePremiumLevelAssetComponent $savePremiumLevelAssetComponent
     * @param AssetRepositoryInterface       $assetRepository
     */
    public function __construct(
        SaveCategoryAssetComponent $saveCategoryAssetComponent,
        SaveCreatorAssetComponent $saveCreatorAssetComponent,
        SaveMediaTypeAssetComponent $saveMediaTypeAssetComponent,
        SavePremiumLevelAssetComponent $savePremiumLevelAssetComponent,
        AssetRepositoryInterface $assetRepository
    ) {
        $this->saveCategoryAssetComponent = $saveCategoryAssetComponent;
        $this->saveCreatorAssetComponent = $saveCreatorAssetComponent;
        $this->saveMediaTypeAssetComponent= $saveMediaTypeAssetComponent;
        $this->savePremiumLevelAssetComponent = $savePremiumLevelAssetComponent;
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
            $mediaTypeAssetComponent = $this->saveMediaTypeAssetComponent->execute($asset->getMediaType());
            $premiumLevelAssetComponent = $this->savePremiumLevelAssetComponent->execute($asset->getPremiumLevel());

            $asset->setAdobeId($asset->getId());
            $asset->setId(null);
            $asset->setData(AssetInterface::CATEGORY_ID, $categoryAssetComponent->getId());
            $asset->setData(AssetInterface::CREATOR_ID, $creatorAssetComponent->getId());
            $asset->setData(AssetInterface::MEDIA_TYPE_ID, $mediaTypeAssetComponent->getId());
            $asset->setData(AssetInterface::PREMIUM_LEVEL_ID, $premiumLevelAssetComponent->getId());
            $this->assetRepository->save($asset);

            return $asset;
        } catch (AlreadyExistsException $exception) {
            return $asset;
        }
    }
}
