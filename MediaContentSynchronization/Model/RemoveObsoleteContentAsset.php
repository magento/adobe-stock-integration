<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Magento\MediaContentSynchronization\Model;

use Magento\MediaGallerySynchronizationApi\Model\FetchBatchesInterface;
use Magento\Framework\App\ResourceConnection;
use Magento\MediaContentApi\Api\DeleteContentAssetLinksByAssetIdsInterface;

/**
 * Remove obsolete content asset from deleted entities
 */
class RemoveObsoleteContentAsset
{
    private const MEDIA_CONTENT_ASSET_TABLE = 'media_content_asset';
    private const DEFAUL_IDENTITY_FIELD = 'entity_Id';
    /**
     * @var FetchBatchesInterface
     */
    private $fetchBatches;
    
    /**
     * @var ResourceConnection
     */
    private $resourceConnection;

    /**
     * @var DeleteContentAssetLinksByAssetIdsInterface
     */
    private $deleteContentAssetLinksByAssetIds;

    /**
     * @var array
     */
    private $entities;

    /**
     * @var array $entityTableNames
     */
    private $entityTableNames;

    /**
     * @var array $identityFields
     */
    private $identityFields;
    
    /**
     * @param DeleteContentAssetLinksByAssetIdsInterface $deleteContentAssetLinks
     * @param FetchBatchesInterface $fetchBatches
     * @param ResourceConnection $resourceConnection
     * @param array $entities
     * @param array $entityTableNames
     */
    public function __construct(
        DeleteContentAssetLinksByAssetIdsInterface $deleteContentAssetLinks,
        FetchBatchesInterface $fetchBatches,
        ResourceConnection $resourceConnection,
        array $identityFields = [],
        array $entities = [],
        array $entityTableNames = []
    ) {
        $this->deleteContentAssetLinksByAssetIds = $deleteContentAssetLinks;
        $this->fetchBatches = $fetchBatches;
        $this->resourceConnection = $resourceConnection;
        $this->entities = $entities;
        $this->identityFIelds = $identityFields;
        $this->entityTableNames = $entityTableNames;
    }

    /**
     * Remove media content if entity already deleted.
     */
    public function execute(): void
    {
        foreach ($this->entities as $entity) {
            $columns = ['entity_id', 'entity_type', 'asset_id'];
            foreach ($this->fetchBatches->execute(self::MEDIA_CONTENT_ASSET_TABLE, $columns) as $batch) {
                $assetIds = [];
                foreach ($batch as $item) {
                    if ($item['entity_type'] !== $entity) {
                        continue;
                    }
                    if (!$this->isEntityExist($this->entityTableNames[$entity], (int) $item['entity_id'])) {
                        $assetIds[] = $item['asset_id'];
                    }
                }
                $this->deleteContentAssetLinksByAssetIds->execute($assetIds);
            }
        }
    }

    /**
     * Verify if product by provided id exists
     *
     * @param string $entityTableName
     * @param int $entityId
     */
    private function isEntityExist(string $entityTableName, int $entityId): bool
    {
        $identityField = isset($this->identityFIelds[$entity]) ?
                       $this->identityFIelds[$entity] :
                       self::DEFAUL_IDENTITY_FIELD;
        $connection = $this->resourceConnection->getConnection();
        $entityTable = $this->resourceConnection->getTableName($entityTableName);

        $select = $connection->select();
        $select->from($entityTable, [$identityField]);
        $select->where($identityField . ' = ?', $entityId . '%');
        return !empty($connection->fetchCol($select));
    }
}
