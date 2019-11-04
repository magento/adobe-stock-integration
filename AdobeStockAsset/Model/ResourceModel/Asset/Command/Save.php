<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Magento\AdobeStockAsset\Model\ResourceModel\Asset\Command;

use Magento\AdobeMediaGalleryApi\Model\DataExtractorInterface;
use Magento\Framework\App\ResourceConnection;
use Magento\AdobeStockAssetApi\Api\Data\AssetInterface;
use Magento\Framework\DB\Adapter\AdapterInterface;

/**
 * Save multiple asset service.
 */
class Save
{
    private const ADOBE_STOCK_ASSET_TABLE_NAME = 'adobe_stock_asset';

    /**
     * @var ResourceConnection
     */
    private $resourceConnection;

    /**
     * @var DataExtractorInterface
     */
    private $dataExtractor;

    /**
     * @param ResourceConnection $resourceConnection
     * @param DataExtractorInterface $dataExtractor
     */
    public function __construct(
        ResourceConnection $resourceConnection,
        DataExtractorInterface $dataExtractor
    ) {
        $this->resourceConnection = $resourceConnection;
        $this->dataExtractor = $dataExtractor;
    }

    /**
     * Save asset
     *
     * @param AssetInterface $asset
     * @return void
     */
    public function execute(AssetInterface $asset): void
    {
        $data = $this->dataExtractor->extract($asset, AssetInterface::class);
        $tableName = $this->resourceConnection->getTableName(self::ADOBE_STOCK_ASSET_TABLE_NAME);

        if (empty($data)) {
            return;
        }

        $data = $this->filterData($data, array_keys($this->getConnection()->describeTable($tableName)));
        $this->getConnection()->insertOnDuplicate($tableName, $data);
    }

    /**
     * Filter data to keep only data for columns specified
     *
     * @param array $data
     * @param array $columns
     * @return array
     */
    private function filterData(array $data, array $columns): array
    {
        return array_intersect_key($data, array_flip($columns));
    }

    /**
     * Retrieve DB adapter
     *
     * @return AdapterInterface
     */
    private function getConnection(): AdapterInterface
    {
        return $this->resourceConnection->getConnection();
    }
}
