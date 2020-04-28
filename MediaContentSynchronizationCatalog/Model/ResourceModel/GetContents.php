<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Magento\MediaContentSynchronizationCatalog\Model\ResourceModel;

use Magento\Eav\Model\Config;
use Magento\Framework\App\ResourceConnection;
use Magento\Framework\Exception\LocalizedException;

/**
 * Retrieve array of concatenated contents for entity attribute
 */
class GetContents
{
    /**
     * @var ResourceConnection
     */
    private $resourceConnection;

    /**
     * @var Config
     */
    private $config;

    /**
     * @param Config $config
     * @param ResourceConnection $resourceConnection
     */
    public function __construct(
        Config $config,
        ResourceConnection $resourceConnection
    ) {
        $this->config = $config;
        $this->resourceConnection = $resourceConnection;
    }

    /**
     * Get contents information form database
     *
     * @param string $entityType
     * @param string $field
     * @param string $idField
     * @return array
     * @throws LocalizedException
     */
    public function execute(string $entityType, string $field, string $idField): array
    {
        $connection = $this->resourceConnection->getConnection();

        $attribute = $this->config->getAttribute($entityType, $field);
        $select = $connection->select()->from(
            ['abt' => $attribute->getBackendTable()],
            [
                'content_type' => new \Zend_Db_Expr("'" . $entityType . "'"),
                'entity_id' => new \Zend_Db_Expr('abt.' . $idField),
                'field' => new \Zend_Db_Expr("'$field'"),
                'content' => new \Zend_Db_Expr("GROUP_CONCAT(abt.value SEPARATOR'" . PHP_EOL . "')")
            ]
        )->where(
            $connection->quoteIdentifier('abt.attribute_id') . ' = ?',
            $attribute->getAttributeId()
        )->group(
            'abt.entity_id'
        )->distinct(true);

        return $connection->fetchAll($select);
    }
}
