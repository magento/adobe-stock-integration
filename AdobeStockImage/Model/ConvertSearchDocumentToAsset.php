<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

declare(strict_types=1);

namespace Magento\AdobeStockImage\Model;

use Magento\AdobeStockAssetApi\Api\Data\AssetInterface;
use Magento\AdobeStockAssetApi\Api\Data\AssetInterfaceFactory;
use Magento\AdobeStockAssetApi\Api\Data\CategoryInterface;
use Magento\AdobeStockAsset\Model\Components\CategoryComponentFactory;
use Magento\AdobeStockAssetApi\Api\Data\CreatorInterface;
use Magento\AdobeStockAsset\Model\Components\CreatorComponentFactory;
use Magento\AdobeStockAssetApi\Api\Data\MediaTypeInterface;
use Magento\AdobeStockAsset\Model\Components\MediaTypeComponentFactory;
use Magento\AdobeStockAssetApi\Api\Data\PremiumLevelInterface;
use Magento\AdobeStockAsset\Model\Components\PremiumLevelComponentFactory;
use Magento\Framework\Api\Search\Document;
use Magento\Framework\Exception\IntegrationException;
use Magento\Payment\Gateway\Http\ConverterException;

/**
 * Class ConvertSearchDocumentToAsset
 */
class ConvertSearchDocumentToAsset
{
    /**
     * @var AssetInterfaceFactory
     */
    private $assetFactory;

    /**
     * @var CategoryComponentFactory
     */
    private $categoryComponentFactory;

    /**
     * @var CreatorComponentFactory
     */
    private $creatorComponentFactory;

    /**
     * @var MediaTypeComponentFactory
     */
    private $mediaTypeComponentFactory;

    /**
     * @var PremiumLevelComponentFactory
     */
    private $premiumLevelComponentFactory;

    /**
     * ConvertSearchDocumentToAsset constructor.
     *
     * @param AssetInterfaceFactory        $assetFactory
     * @param CategoryComponentFactory     $categoryComponentFactory
     * @param CreatorComponentFactory      $creatorComponentFactory
     * @param MediaTypeComponentFactory    $mediaTypeComponentFactory
     * @param PremiumLevelComponentFactory $premiumLevelComponentFactory
     */
    public function __construct(
        AssetInterfaceFactory $assetFactory,
        CategoryComponentFactory $categoryComponentFactory,
        CreatorComponentFactory $creatorComponentFactory,
        MediaTypeComponentFactory $mediaTypeComponentFactory,
        PremiumLevelComponentFactory $premiumLevelComponentFactory
    ) {
        $this->assetFactory = $assetFactory;
        $this->categoryComponentFactory = $categoryComponentFactory;
        $this->creatorComponentFactory = $creatorComponentFactory;
        $this->mediaTypeComponentFactory = $mediaTypeComponentFactory;
        $this->premiumLevelComponentFactory = $premiumLevelComponentFactory;
    }

    /**
     * Convert search document to asset instance.
     *
     * @param Document $document
     *
     * @return AssetInterface
     * @throws ConverterException
     */
    public function execute(Document $document): AssetInterface
    {
        try {
            /** @var AssetInterface $asset */
            $asset = $this->assetFactory->create();

            $categoryAssetComponent = $this->hydrateCategoryComponentData($document);
            $asset->setCategory($categoryAssetComponent);
            $creatorAssetComponent = $this->hydrateCreatorComponentData($document);
            $asset->setCreator($creatorAssetComponent);
            $mediaTypeAssetComponent = $this->hydrateMediaTypeComponentData($document);
            $asset->setMediaType($mediaTypeAssetComponent);
            $premiumLevelAssetComponent = $this->hydratePremiumLevelComponentData($document);
            $asset->setPremiumLevel($premiumLevelAssetComponent);

            $asset->setId($document->getId());
            $asset->setPreviewUrl($document->getCustomAttribute(AssetInterface::PREVIEW_URL)->getValue());
            $asset->setHeight($document->getCustomAttribute(AssetInterface::HEIGHT)->getValue());
            $asset->setWidth($document->getCustomAttribute(AssetInterface::WIDTH)->getValue());
            $asset->setContentType($document->getCustomAttribute(AssetInterface::CONTENT_TYPE)->getValue());
            $asset->setCountryName($document->getCustomAttribute(AssetInterface::COUNTRY_NAME)->getValue());
            $asset->setCreationDate($document->getCustomAttribute(AssetInterface::CREATION_DATE)->getValue());
            $asset->setDetailsUrl($document->getCustomAttribute(AssetInterface::DETAILS_URL)->getValue());

            $asset->setThumbnailUrl($document->getCustomAttribute('thumbnail_url')->getValue());

            $keywords = $this->getKeywords($document->getCustomAttribute(AssetInterface::KEYWORDS)->getValue());
            $asset->setKeywords($keywords);

            return $asset;
        } catch (\Exception $exception) {
            $message = __('Convert search document to asset failed: %1', $exception->getMessage());
            throw new ConverterException($message, $exception);
        }
    }

    /**
     * Hydrate asset category component data from search document.
     *
     * @param Document $document
     *
     * @return CategoryInterface
     * @throws IntegrationException
     */
    private function hydrateCategoryComponentData(Document $document): CategoryInterface
    {
        $categoryData = $document->getCustomAttribute(AssetInterface::CATEGORY);

        /** @var CategoryInterface $categoryComponent */
        $categoryComponent = $this->categoryComponentFactory->create(
            $categoryData->getValue()->getId(),
            $categoryData->getValue()->getName()
        );

        return $categoryComponent;
    }

    /**
     *  Hydrate asset creator component data from search document.
     *
     * @param Document $document
     *
     * @return CreatorInterface
     * @throws IntegrationException
     */
    private function hydrateCreatorComponentData(Document $document): CreatorInterface
    {
        /** @var CreatorInterface $creatorComponent */
        $creatorComponent = $this->creatorComponentFactory->create(
            $document->getCustomAttribute('creator_id')->getValue(),
            $document->getCustomAttribute('creator_name')->getValue()
        );

        return $creatorComponent;
    }

    /**
     * Hydrate asset media type component data from search document.
     *
     * @param Document $document
     *
     * @return MediaTypeInterface
     * @throws IntegrationException
     */
    private function hydrateMediaTypeComponentData(Document $document): MediaTypeInterface
    {
        /** @var MediaTypeInterface $mediaTypeComponent */
        $mediaTypeComponent = $this->mediaTypeComponentFactory->create(
            $document->getCustomAttribute('creator_id')->getValue(),
            ''
        );

        return $mediaTypeComponent;
    }

    /**
     * Hydrate asset premium level component data from search document.
     *
     * @param Document $document
     *
     * @return PremiumLevelInterface
     * @throws IntegrationException
     */
    private function hydratePremiumLevelComponentData(Document $document): PremiumLevelInterface
    {
        /** @var PremiumLevelInterface $mediaTypeComponent */
        $premiumLevelComponent = $this->premiumLevelComponentFactory->create(
            $document->getCustomAttribute('premium_level_id')->getValue(),
            ''
        );

        return $premiumLevelComponent;
    }

    /**
     * Get keywords data from
     *
     * @param array $keywordsData
     *
     * @return array
     */
    private function getKeywords(array $keywordsData): array
    {
        $keywords = [];
        foreach ($keywordsData as $key => $value) {
            $keywords[] = $value->getName();
        }

        return $keywords;
    }
}
