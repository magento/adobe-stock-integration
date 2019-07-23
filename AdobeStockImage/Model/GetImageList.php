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
use Magento\AdobeStockAssetApi\Api\Data\CategoryInterfaceFactory;
use Magento\AdobeStockAssetApi\Api\Data\CreatorInterface;
use Magento\AdobeStockAssetApi\Api\Data\CreatorInterfaceFactory;
use Magento\AdobeStockAssetApi\Api\Data\MediaTypeInterface;
use Magento\AdobeStockAssetApi\Api\Data\MediaTypeInterfaceFactory;
use Magento\AdobeStockAssetApi\Api\Data\AssetSearchResultsInterface;
use Magento\AdobeStockAssetApi\Api\Data\AssetSearchResultsInterfaceFactory as SearchResultFactory;
use Magento\AdobeStockAssetApi\Api\Data\PremiumLevelInterface;
use Magento\AdobeStockAssetApi\Api\Data\PremiumLevelInterfaceFactory;
use Magento\AdobeStockClientApi\Api\ClientInterface;
use Magento\AdobeStockImageApi\Api\GetImageListInterface;
use Magento\Framework\Api\SearchCriteriaInterface;
use Magento\Framework\Exception\AuthenticationException;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Api\Search\Document;
use Magento\Framework\UrlInterface;

/**
 * Class GetImageList
 */
class GetImageList implements GetImageListInterface
{
    /**
     * @var ClientInterface
     */
    private $client;

    /**
     * @var AssetInterfaceFactory
     */
    private $assetFactory;

    /**
     * @var SearchResultFactory
     */
    private $searchResultFactory;

    /**
     * @var CategoryInterfaceFactory
     */
    private $categoryFactory;

    /**
     * @var CreatorInterfaceFactory
     */
    private $creatorFactory;

    /**
     * @var MediaTypeInterfaceFactory
     */
    private $mediaTypeFactory;

    /**
     * @var PremiumLevelInterfaceFactory
     */
    private $premiumLevelFactory;

    /**
     * @var UrlInterface
     */
    private $url;

    /**
     * GetImageList constructor.
     *
     * @param ClientInterface              $client
     * @param AssetInterfaceFactory        $assetFactory
     * @param SearchResultFactory          $searchResultFactory
     * @param UrlInterface                 $url
     * @param CategoryInterfaceFactory     $categoryFactory
     * @param CreatorInterfaceFactory      $creatorFactory
     * @param MediaTypeInterfaceFactory    $mediaTypeFactory
     * @param PremiumLevelInterfaceFactory $premiumLevelFactory
     */
    public function __construct(
        ClientInterface $client,
        AssetInterfaceFactory $assetFactory,
        SearchResultFactory $searchResultFactory,
        UrlInterface $url,
        CategoryInterfaceFactory $categoryFactory,
        CreatorInterfaceFactory $creatorFactory,
        MediaTypeInterfaceFactory $mediaTypeFactory,
        PremiumLevelInterfaceFactory $premiumLevelFactory
    ) {
        $this->client = $client;
        $this->assetFactory = $assetFactory;
        $this->searchResultFactory = $searchResultFactory;
        $this->url = $url;
        $this->categoryFactory = $categoryFactory;
        $this->creatorFactory = $creatorFactory;
        $this->mediaTypeFactory = $mediaTypeFactory;
        $this->premiumLevelFactory = $premiumLevelFactory;
    }

    /**
     * @inheritdoc
     */
    public function execute(SearchCriteriaInterface $searchCriteria): AssetSearchResultsInterface
    {
        try {
            $searchResult = $this->client->search($searchCriteria);

            $items = [];
            /** @var Document $item */
            foreach ($searchResult->getItems() as $item) {
                /** @var AssetInterface $asset */
                $asset = $this->convertDocumentToAsset($item);
                $items[] = $asset;
            }
            return $this->searchResultFactory->create(
                [
                    'data' => [
                        'items' => $items,
                        'total_count' => $searchResult->getTotalCount(),
                    ],
                ]
            );
        } catch (AuthenticationException $exception) {
            throw new LocalizedException(
                __(
                    'Failed to authenticate to Adobe Stock API. Please correct the API credentials in '
                    . '<a href="%1">Configuration → System → Adobe Stock Integration.</a>',
                    $this->url->getUrl('adminhtml/system_config/edit/section/system')
                )
            );
        } catch (\Exception $exception) {
            $message = __('Get image list action failed.');
            throw new LocalizedException($message, $exception, $exception->getCode());
        }
    }

    /**
     * Generate Asset object
     *
     * @param Document $item
     *
     * @return AssetInterface
     */
    private function convertDocumentToAsset(Document $item): AssetInterface
    {
        //@TODO granulate this to components and make asset part construction possibly in builder

        /** @var AssetInterface $asset */
        $asset = $this->assetFactory->create();

        $asset->setId($item->getId());
        $asset->setThumbnailUrl($item->getCustomAttribute('thumbnail_url')->getValue());
        $asset->setPreviewUrl($item->getCustomAttribute('preview_url')->getValue());
        $asset->setHeight($item->getCustomAttribute('height')->getValue());
        $asset->setWidth($item->getCustomAttribute('width')->getValue());

        /** @var CategoryInterface $category */
        $category = $this->categoryFactory->create();
        $categoryData = $item->getCustomAttribute('category');
        $category->setAdobeId($categoryData->getValue()->getId());
        $category->setName($categoryData->getValue()->getName());
        $asset->setCategory($category);

        /** @var CreatorInterface $creator */
        $creator = $this->creatorFactory->create();
        $creator->setAdobeId($item->getCustomAttribute('creator_id')->getValue());
        $creator->setName($item->getCustomAttribute('creator_name')->getValue());
        $asset->setCreator($creator);

        /** @var MediaTypeInterface $mediaType */
        $mediaType = $this->mediaTypeFactory->create();
        $mediaType->setAdobeId($item->getCustomAttribute('media_type_id')->getValue());
        $asset->setMediaType($mediaType);

        /** @var PremiumLevelInterface $premiumLevel */
        $premiumLevel = $this->premiumLevelFactory->create();
        $premiumLevel->setAdobeId($item->getCustomAttribute('premium_level_id')->getValue());
        $asset->setPremiumLevel($premiumLevel);

        $asset->setCreationDate($item->getCustomAttribute('creation_date')->getValue());
        $asset->setCountryName($item->getCustomAttribute('country_name')->getValue());
        $asset->setContentType($item->getCustomAttribute('content_type')->getValue());
        $asset->setDetailsUrl($item->getCustomAttribute('details_url')->getValue());

        $keywords = $this->getKeywords($item->getCustomAttribute('keywords')->getValue());
        $asset->setKeywords($keywords);

        return $asset;
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
