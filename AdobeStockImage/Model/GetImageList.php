<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

declare(strict_types=1);

namespace Magento\AdobeStockImage\Model;

use Magento\AdobeStockAssetApi\Api\Data\AssetInterface;
use Magento\AdobeStockAssetApi\Api\Data\AssetInterfaceFactory;
use Magento\AdobeStockAssetApi\Api\Data\AssetSearchResultsInterface;
use Magento\AdobeStockAssetApi\Api\Data\AssetSearchResultsInterfaceFactory as SearchResultFactory;
use Magento\AdobeStockClientApi\Api\ClientInterface;
use Magento\AdobeStockImageApi\Api\GetImageListInterface;
use Magento\Framework\Api\SearchCriteriaInterface;
use Magento\Framework\Exception\AuthenticationException;
use Magento\Framework\Exception\LocalizedException;
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
     * @var UrlInterface
     */
    private $url;

    /**
     * GetImageList constructor.
     *
     * @param ClientInterface $client
     * @param AssetInterfaceFactory $assetFactory
     * @param SearchResultFactory $searchResultFactory
     * @param UrlInterface $url
     */
    public function __construct(
        ClientInterface $client,
        AssetInterfaceFactory $assetFactory,
        SearchResultFactory $searchResultFactory,
        UrlInterface $url
    ) {
        $this->client = $client;
        $this->assetFactory = $assetFactory;
        $this->searchResultFactory = $searchResultFactory;
        $this->url = $url;
    }

    /**
     * @inheritdoc
     */
    public function execute(SearchCriteriaInterface $searchCriteria): AssetSearchResultsInterface
    {
        try {
            $searchResult = $this->client->search($searchCriteria);

            $items = [];
            foreach ($searchResult->getItems() as $item) {
                /** @var AssetInterface $asset */
                $asset = $this->assetFactory->create();
                $asset->setId($item->getId());
                $asset->setThumbnailUrl($item->getCustomAttribute('thumbnail_url')->getValue());
                $asset->setPreviewUrl($item->getCustomAttribute('preview_url')->getValue());
                $asset->setHeight($item->getCustomAttribute('height')->getValue());
                $asset->setWidth($item->getCustomAttribute('width')->getValue());
                $items[] = $asset;
            }
            return $this->searchResultFactory->create(
                [
                    'data' => [
                        'items' => $items,
                        'total_count' => $searchResult->getTotalCount()
                    ]
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
}
