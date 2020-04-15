<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magento\MediaGalleryUi\Ui\Component\Listing;

use Magento\Framework\Data\Collection\Db\FetchStrategyInterface as FetchStrategy;
use Magento\Framework\Data\Collection\EntityFactoryInterface as EntityFactory;
use Magento\Framework\Event\ManagerInterface as EventManager;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\View\Element\UiComponent\DataProvider\SearchResult;
use Magento\MediaGalleryApi\Api\Data\AssetInterface;
use Magento\MediaGalleryApi\Api\Data\KeywordInterface;
use Magento\MediaGalleryApi\Model\Keyword\Command\GetAssetKeywordsInterface;
use Psr\Log\LoggerInterface as Logger;

class Provider extends SearchResult
{
    /**
     * @var GetAssetKeywordsInterface
     */
    private $getAssetKeywords;

    /**
     * @param EntityFactory $entityFactory
     * @param Logger $logger
     * @param FetchStrategy $fetchStrategy
     * @param EventManager $eventManager
     * @param GetAssetKeywordsInterface $getAssetKeywords
     * @param string $mainTable
     * @param null|string $resourceModel
     * @param null|string $identifierName
     * @param null|string $connectionName
     * @throws LocalizedException
     */
    public function __construct(
        EntityFactory $entityFactory,
        Logger $logger,
        FetchStrategy $fetchStrategy,
        EventManager $eventManager,
        GetAssetKeywordsInterface $getAssetKeywords,
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
    }

    /**
     * @inheritdoc
     */
    public function getData()
    {
        $data = parent::getData();
        $keywords = [];
        foreach ($this->_items as $asset) {
            //TODO: Must be replaced with new bulk interface: \Magento\MediaGalleryApi\Api\GetAssetsKeywordsInterface
            $keywords[$asset->getId()] = array_map(function (KeywordInterface $keyword) {
                return $keyword->getKeyword();
            }, $this->getAssetKeywords->execute($asset->getId()));
        }

        /** @var AssetInterface $asset */
        foreach ($data as $key => $asset) {
            $data[$key]['thumbnail_url'] = $asset['path'];
            $data[$key]['preview_url'] = $asset['path'];
            $data[$key]['keywords'] = isset($keywords[$asset['id']]) ? implode(",", $keywords[$asset['id']]) : '';
            $data[$key]['source'] = empty($asset['source']) ? 'Local' : $asset['source'];
        }
        return $data;
    }
}
