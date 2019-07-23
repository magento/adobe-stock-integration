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
     * Execute save asset process.
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

            $asset->setCategory($categoryAssetComponent);
            $asset->setCreator($creatorAssetComponent);
            $asset->setMediaType($mediaTypeAssetComponent);

            $asset->setAdobeId($asset->getId());
            $asset->setId(null);
            $asset->setData('category_id', $categoryAssetComponent->getId());
            $asset->setData('creator_id', $creatorAssetComponent->getId());
            $asset->setData('media_type_id', $mediaTypeAssetComponent->getId());
            $asset->setData('premium_level_id', $premiumLevelAssetComponent->getId());
            $this->assetRepository->save($asset);

            //@TODO save keywords for newly added asset

            return $asset;
        } catch (AlreadyExistsException $exception) {
            return $asset;
        }
    }
}
