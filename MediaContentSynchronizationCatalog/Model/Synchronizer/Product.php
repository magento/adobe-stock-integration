<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Magento\MediaContentSynchronizationCatalog\Model\Synchronizer;

use Magento\MediaContentSynchronizationApi\Api\SynchronizerInterface;
use Magento\Eav\Model\Config;
use Magento\Framework\App\ResourceConnection;
use Psr\Log\LoggerInterface;
use Magento\Catalog\Api\Data\ProductInterface;
use Magento\Framework\EntityManager\MetadataPool;

/**
 * Synchronize content with assets
 */
class Product implements SynchronizerInterface
{
    private const ENTITY = 'catalog_product';

    /**
     * @var MetadataPool
     */
    private $metadataPool;

    /**
     * @var ResourceConnection
     */
    private $resourceConnection;

    /**
     * @var LoggerInterface
     */
    private $log;

    /**
     * @var array
     */
    private $fields;

    /**
     * @var Config
     */
    private $config;

    /**
     * @param LoggerInterface $log
     * @param Config $config
     * @param ResourceConnection $resourceConnection
     * @param MetadataPool $metadataPool
     * @param array $fields
     */
    public function __construct(
        LoggerInterface $log,
        Config $config,
        ResourceConnection $resourceConnection,
        MetadataPool $metadataPool,
        array $fields = []
    ) {
        $this->log = $log;
        $this->metadataPool = $metadataPool;
        $this->fields = $fields;
        $this->config = $config;
        $this->resourceConnection = $resourceConnection;
    }

    /**
     * @inheritdoc
     */
    public function execute(): array
    {
        $connection = $this->resourceConnection->getConnection();
        $entityIdField = $this->metadataPool->getMetadata(ProductInterface::class)->getLinkField();
        $contents = [];

        foreach ($this->fields as $field) {
            $attribute = $this->config->getAttribute(self::ENTITY, $field);
            $select = $connection->select()->from(
                ['abt' => $attribute->getBackendTable()],
                [
                    'content_type' => new \Zend_Db_Expr("'" . self::ENTITY . "'"),
                    'entity_id' => new \Zend_Db_Expr('abt.' . $entityIdField),
                    'field' => new \Zend_Db_Expr("'$field'"),
                    'content' => new \Zend_Db_Expr("GROUP_CONCAT(abt.value SEPARATOR'" . PHP_EOL . "')")
                ]
            )->where(
                $connection->quoteIdentifier('abt.attribute_id') . ' = ?',
                $attribute->getAttributeId()
            )->group(
                'abt.entity_id'
            )->distinct(true);

            $contents = array_merge($contents, $connection->fetchAll($select));
        }

        return $contents;
    }
}
