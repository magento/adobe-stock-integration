<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magento\MediaGalleryUi\Ui\Component\Listing;

use Exception;
use Magento\Framework\Data\Collection\Db\FetchStrategyInterface as FetchStrategy;
use Magento\Framework\Data\Collection\EntityFactoryInterface as EntityFactory;
use Magento\Framework\Event\ManagerInterface as EventManager;
use Magento\Framework\Exception\IntegrationException;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\View\Element\UiComponent\DataProvider\SearchResult;
use Magento\MediaGalleryApi\Api\Data\AssetInterface;
use Magento\MediaGalleryApi\Api\Data\AssetKeywordsInterface;
use Magento\MediaGalleryApi\Api\Data\KeywordInterface;
use Magento\MediaGalleryApi\Api\GetAssetsByIdsInterface;
use Magento\MediaGalleryApi\Api\GetAssetsKeywordsInterface;
use Magento\MediaGalleryUi\Model\AssetDetailsProvider\UsedIn;
use Psr\Log\LoggerInterface as Logger;

class Provider extends SearchResult
{
    /**
     * @var GetAssetsKeywordsInterface
     */
    private $getAssetKeywords;
    /**
     * @var GetAssetsByIdsInterface
     */
    private $getAssetsByIds;
    /**
     * @var UsedIn
     */
    private $usedIn;

    /**
     * @param EntityFactory $entityFactory
     * @param Logger $logger
     * @param FetchStrategy $fetchStrategy
     * @param EventManager $eventManager
     * @param GetAssetsKeywordsInterface $getAssetKeywords
     * @param GetAssetsByIdsInterface $getAssetsByIds
     * @param UsedIn $usedIn
     * @param string $mainTable
     * @param null|string $resourceModel
     * @param null|string $identifierName
     * @param null|string $connectionName
     * @throws LocalizedException
     * @SuppressWarnings(PHPMD.ExcessiveParameterList)
     */
    public function __construct(
        EntityFactory $entityFactory,
        Logger $logger,
        FetchStrategy $fetchStrategy,
        EventManager $eventManager,
        GetAssetsKeywordsInterface $getAssetKeywords,
        GetAssetsByIdsInterface $getAssetsByIds,
        UsedIn $usedIn,
        $mainTable = 'media_gallery_asset',
        $resourceModel = null,
        $identifierName = null,
        $connectionName = null
    ) {
        parent::__construct(
            $entityFactory,
            $logger,
            $fetchStrategy,
            $eventManager,
            $mainTable,
            $resourceModel,
            $identifierName,
            $connectionName
        );
        $this->getAssetKeywords = $getAssetKeywords;
        $this->getAssetsByIds = $getAssetsByIds;
        $this->usedIn = $usedIn;
    }

    /**
     * @inheritdoc
     */
    public function getData()
    {
        $data = parent::getData();
        $keywords = [];
        foreach ($this->_items as $asset) {
            $keywords[$asset->getId()] = array_map(function (AssetKeywordsInterface $assetKeywords) {
                return array_map(function (KeywordInterface $keyword) {
                    return $keyword->getKeyword();
                }, $assetKeywords->getKeywords());
            }, $this->getAssetKeywords->execute([$asset->getId()]));
        }

        /** @var AssetInterface $asset */
        foreach ($data as $key => $asset) {
            try {
                $data[$key]['thumbnail_url'] = $asset['path'];
                $data[$key]['content_type'] = strtoupper(str_replace('image/', '', $asset['content_type']));
                $data[$key]['preview_url'] = $asset['path'];
                $data[$key]['keywords'] = isset($keywords[$asset['id']]) ? implode(",", $keywords[$asset['id']]) : '';
                $data[$key]['source'] = empty($asset['source']) ? 'Local' : $asset['source'];
                $data[$key]['relatedContent'] = $this->getRelatedContent($asset['id']);
            } catch (Exception $exception) {
                $this->_logger->error($exception->getMessage());
            }
        }
        return $data;
    }

    /**
     * Returns information about related content
     *
     * @param int $assetId
     * @return array
     * @throws LocalizedException
     * @throws IntegrationException
     */
    private function getRelatedContent(int $assetId): array
    {
        $assetItem = $this->getAssetsByIds->execute([$assetId]);

        return isset($assetItem[0]) ? $this->usedIn->execute($assetItem[0]) : [];
    }
}
