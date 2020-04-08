<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magento\MediaGalleryUi\Ui\Component\Listing;

use Magento\Framework\Data\Collection\Db\FetchStrategyInterface as FetchStrategy;
use Magento\Framework\Data\Collection\EntityFactoryInterface as EntityFactory;
use Magento\Framework\Event\ManagerInterface as EventManager;
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
     * @param $mainTable
     * @param GetAssetKeywordsInterface $getAssetKeywords
     * @param null $resourceModel
     * @param null $identifierName
     * @param null $connectionName
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function __construct(
        EntityFactory $entityFactory,
        Logger $logger,
        FetchStrategy $fetchStrategy,
        EventManager $eventManager,
        $mainTable,
        GetAssetKeywordsInterface $getAssetKeywords,
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
    protected function _afterLoad()
    {
        parent::_afterLoad();
        $keywords = $this->loadAssetsKeywords($this->_items);

        /** @var AssetInterface $asset */
        foreach ($this->_items as $key => $asset) {
            $this->_items[$key]->setData('thumbnail_url', $asset->getPath());
            $this->_items[$key]->setData('preview_url', $asset->getPath());
            $this->_items[$key]->setData(
                'keywords',
                isset($keywords[$asset->getId()]) ? implode(",",  $keywords[$asset->getId()]) : ''
            );
        }
        return $this;
    }

    /**
     * @param array $assets
     * @return array
     */
    private function loadAssetsKeywords(array $assets) : array
    {
        //TODO: Must be replaced with new bulk interface: \Magento\MediaGalleryApi\Api\GetAssetsKeywordsInterface
        $result = [];
        foreach ($assets as $asset) {
            $result[$asset->getId()] = array_map(function (KeywordInterface $keyword) {
                return $keyword->getKeyword();
            }, $this->getAssetKeywords->execute($asset->getId()));
        }
        return $result;
    }
}
