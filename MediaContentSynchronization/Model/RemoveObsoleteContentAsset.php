<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Magento\MediaContentSynchronization\Model;

use Magento\Framework\App\ResourceConnection;
use Magento\MediaContentSynchronizationApi\Model\GetEntitiesInterface;
use Magento\Framework\EntityManager\MetadataPool;
use Magento\Framework\Exception\CouldNotDeleteException;
use Psr\Log\LoggerInterface;

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
     * @var GetEntitiesInterface
     */
    private $getEntities;

    /**
     * @var MetadataPool
     */
    private $metadataPool;

    /**
     * @var LoggerInterface
     */
    private $logger;
    
    /**
     * @param MetadataPool $metadataPool
     * @param ResourceConnection $resourceConnection
     * @param GetEntitiesInterface $getEntities
     * @param LoggerInterface $logger
     */
    public function __construct(
        MetadataPool $metadataPool,
        ResourceConnection $resourceConnection,
        GetEntitiesInterface $getEntities,
        LoggerInterface $logger
    ) {
        $this->metadataPool = $metadataPool;
        $this->resourceConnection = $resourceConnection;
        $this->getEntities = $getEntities;
        $this->logger = $logger;
    }

    /**
     * Remove media content if entity already deleted.
     */
    public function execute(): void
    {
        foreach ($this->getEntities->execute() as $entity) {
            $assets = $this->getRemovedAssets($entity);
            if (!empty($assets)) {
                $this->deleteObsoleteContentAsset($assets);
            }
        }
    }

    /**
     * Remove obsolete asset content links by assets id and entity id.
     *
     * @param array $assets
     */
    private function deleteObsoleteContentAsset(array $assets): void
    {
        try {
            $this->resourceConnection->getConnection()->delete(
                self::MEDIA_CONTENT_ASSET_TABLE,
                [
                    'asset_id IN (?)' => implode(",", array_column($assets, 'asset_id')),
                    'entity_id IN (?)' => implode(",", array_column($assets, 'entity_id'))
                ]
            );
        } catch (\Exception $exception) {
            $this->logger->critical($exception);
            $message = __('Could not delete media content links');
            throw new CouldNotDeleteException($message, $exception);
        }
    }
    
    /**
     * Returns records from media_content table in wichs entity_id not exist anymore.
     *
     * @param string $entityType
     * @throws CouldNotDeleteException
     */
    private function getRemovedAssets(string $entityType): array
    {
        try {
            $entityData = $this->metadataPool->getMetadata($entityType);
            $connection = $this->resourceConnection->getConnection();
            $mediaContentTable = $this->resourceConnection->getTableName(self::MEDIA_CONTENT_ASSET_TABLE);
            $select = $connection->select();
            
            $select->from(['mca' => $mediaContentTable], ['asset_id', 'entity_id',  'entity_type', 'field']);
            $select->joinLeft(
                ['et' => $entityData->getEntityTable()],
                'et.' . $entityData->getIdentifierField() . ' =  mca.entity_id ',
                [$entityData->getIdentifierField(). ' AS entity_identifier']
            );
            $select->where('et.' . $entityData->getIdentifierField() . ' IS NULL');
            $select->where('mca.entity_type = ?', $entityData->getEavEntityType());
            $result =  $connection->fetchAssoc($select);
        } catch (\Exception $exception) {
            $this->logger->critical($exception);
            $message = __('Could not fetch media content links data');
            throw new CouldNotDeleteException($message, $exception);
        }

        return $result;
    }
}
