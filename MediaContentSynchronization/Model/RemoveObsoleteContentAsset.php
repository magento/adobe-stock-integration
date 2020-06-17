<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Magento\MediaContentSynchronization\Model;

use Magento\Framework\App\ResourceConnection;
use Magento\MediaContentApi\Api\DeleteContentAssetLinksByAssetIdsInterface;
use Magento\MediaContentSynchronizationApi\Model\GetEntitiesInterface;
use Magento\Framework\EntityManager\MetadataPool;

/**
 * Remove obsolete content asset from deleted entities
 */
class RemoveObsoleteContentAsset
{
    private const MEDIA_CONTENT_ASSET_TABLE = 'media_content_asset';

    /**
     * @var ResourceConnection
     */
    private $resourceConnection;

    /**
     * @var DeleteContentAssetLinksByAssetIdsInterface
     */
    private $deleteContentAssetLinksByAssetIds;

    /**
     * @var GetEntitiesInterface
     */
    private $getEntities;

    /**
     * @var MetadataPool
     */
    private $metadataPool;
    
    /**
     * @param MetadataPool $metadataPool
     * @param DeleteContentAssetLinksByAssetIdsInterface $deleteContentAssetLinks
     * @param ResourceConnection $resourceConnection
     * @param GetEntitiesInterface $getEntities
     */
    public function __construct(
        MetadataPool $metadataPool,
        DeleteContentAssetLinksByAssetIdsInterface $deleteContentAssetLinks,
        ResourceConnection $resourceConnection,
        GetEntitiesInterface $getEntities
    ) {
        $this->metadataPool = $metadataPool;
        $this->deleteContentAssetLinksByAssetIds = $deleteContentAssetLinks;
        $this->resourceConnection = $resourceConnection;
        $this->getEntities = $getEntities;
    }

    /**
     * Remove media content if entity already deleted.
     */
    public function execute(): void
    {
        foreach ($this->getEntities->execute() as $entity) {
            $this->deleteContentAssetLinksByAssetIds->execute($this->getRemovedAssetIds($entity));
        }
    }

    /**
     * Verify if product by provided id exists
     *
     * @param string $entityType
     */
    private function getRemovedAssetIds(string $entityType): array
    {
        $entityData = $this->metadataPool->getMetadata($entityType);
        $connection = $this->resourceConnection->getConnection();
        $mediaContentTable = $this->resourceConnection->getTableName(self::MEDIA_CONTENT_ASSET_TABLE);
        $select = $connection->select();
        $select->from($mediaContentTable, ['entity_id', 'asset_id', 'entity_type']);
        $select->joinLeft(
            ['et' => $entityData->getEntityTable()],
            'et.' . $entityData->getIdentifierField() . ' = ' . self::MEDIA_CONTENT_ASSET_TABLE . '.entity_id ',
            [$entityData->getIdentifierField()]
        );
        $select->where('et.entity_id IS NULL');

        return $connection->fetchCol($select);
    }
}
