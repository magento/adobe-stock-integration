<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

declare(strict_types=1);

namespace Magento\AdobeStockAsset\Model\ResourceModel;

use Magento\AdobeStockAssetApi\Api\Data\AssetInterface;
use Magento\Framework\Model\AbstractModel;
use Magento\Framework\Model\ResourceModel\Db\AbstractDb;

/**
 * Asset (metadata) resource model
 */
class Asset extends AbstractDb
{
    /**
     * Asset path table
     */
    const ASSET_PATH_TABLE = 'adobe_stock_asset_path';

    /**
     * @inheritdoc
     */
    protected $_isPkAutoIncrement = false;

    /**
     * @inheritdoc
     */
    protected $_useIsObjectNew = true;

    /**
     * Initialize with table name and primary field
     */
    protected function _construct()
    {
        $this->_init('adobe_stock_asset', 'id');
    }

    /**
     * After delete
     *
     * @param AbstractModel|AssetInterface $object
     * @return AbstractDb
     */
    protected function _afterDelete(AbstractModel $object)
    {
        $this->deletePaths($object);

        return parent::_afterDelete($object);
    }

    /**
     * After load
     *
     * @param AbstractModel|AssetInterface $object
     * @return void
     */
    protected function _afterLoad(AbstractModel $object)
    {
        $object->setPaths($this->getPaths((int) $object->getId()));
        parent::_afterLoad($object);
    }

    /**
     * After save
     *
     * @param AbstractModel|AssetInterface $object
     * @return AbstractDb
     */
    protected function _afterSave(AbstractModel $object)
    {
        $this->savePaths($object);

        return parent::_afterSave($object);
    }

    /**
     * Get paths
     *
     * @param int $assetId
     * @return array
     */
    public function getPaths(int $assetId): array
    {
        $connection = $this->getConnection();
        $tableName = $connection->getTableName(self::ASSET_PATH_TABLE);
        $select = $connection->select();
        $select->from($tableName, 'path');
        $select->where('asset_id = ?', $assetId);
        $rows = $connection->fetchAll($select);

        if (!$rows) {
            return [];
        }

        $paths = [];

        foreach ($rows as $row) {
            $path = $row['path'] ?? '';

            if ($path) {
                $paths[] = $path;
            }
        }

        return $paths;
    }

    /**
     * Save paths
     *
     * @param AssetInterface $object
     * @return void
     */
    private function savePaths(AssetInterface $object): void
    {
        $connection = $this->getConnection();
        $assetPathTable = $connection->getTableName(self::ASSET_PATH_TABLE);
        $oldPaths = $this->getPaths($object->getId());
        $newPaths = $object->getPaths();
        $pathsToInsert = array_diff($newPaths, $oldPaths);
        $pathsToDelete = array_diff($oldPaths, $newPaths);

        if ($pathsToInsert) {
            $insertData = [];

            foreach ($pathsToInsert as $pathToInsert) {
                if (!$pathToInsert) {
                    continue;
                }

                $insertData[] = [
                    'asset_id' => (int) $object->getId(),
                    'path' => (string) $pathToInsert
                ];
            }

            $connection->insertMultiple($assetPathTable, $insertData);
        }

        if ($pathsToDelete) {
            $deleteCondition = $connection->prepareSqlCondition(
                sprintf('%s.path', $assetPathTable),
                ['in' => $pathsToDelete]
            );
            $connection->delete($assetPathTable, $deleteCondition);
        }
    }

    /**
     * Delete path
     *
     * @param AssetInterface $object
     * @return void
     */
    private function deletePaths(AssetInterface $object): void
    {
        $connection = $this->getConnection();
        $deleteCondition = $connection->prepareSqlCondition(
            sprintf('%s.asset_id', $connection->getTableName(self::ASSET_PATH_TABLE)),
            $object->getId()
        );
        $connection->delete($deleteCondition);
    }
}
