<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

declare(strict_types=1);

namespace Magento\AdobeStockAsset\Model\ResourceModel\Asset;

use Magento\AdobeStockAsset\Model\Asset as Model;
use Magento\AdobeStockAsset\Model\ResourceModel\Asset as ResourceModel;
use Magento\AdobeStockAssetApi\Api\Data\AssetInterface;
use Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection;

/**
 * Assets (metadata) collection
 */
class Collection extends AbstractCollection
{
    /**
     * Define model & resource model
     */
    protected function _construct()
    {
        $this->_init(
            Model::class,
            ResourceModel::class
        );
    }

    /**
     * Load data
     *
     * @param bool $printQuery
     * @param bool $logQuery
     * @return $this|AbstractCollection
     */
    public function load($printQuery = false, $logQuery = false)
    {
        parent::load($printQuery, $logQuery);
        $this->addPathsToResult();

        return $this;
    }

    /**
     * Add path to result
     *
     * @return void
     */
    private function addPathsToResult(): void
    {
        /** @var ResourceModel $assetResource */
        $assetResource = $this->getResource();

        /** @var AssetInterface $asset */
        foreach ($this->_items as $asset) {
            $paths = $assetResource->getPaths((int) $asset->getId());
            $asset->setPaths($paths);
        }
    }
}
