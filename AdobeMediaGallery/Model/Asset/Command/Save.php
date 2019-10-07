<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Magento\AdobeMediaGallery\Model\Asset\Command;

use Magento\AdobeMediaGalleryApi\Api\Data\AssetInterface;
use Magento\AdobeMediaGalleryApi\Model\Asset\Command\SaveInterface;
use Magento\Framework\App\ResourceConnection;
use Magento\Framework\Exception\CouldNotSaveException;

/**
 * Class Save
 */
class Save implements SaveInterface
{
    private const TABLE_ADOBE_MEDIA_GALLERY = 'adobe_media_gallery';

    /**
     * @var ResourceConnection
     */
    private $resourceConnection;

    /**
     * SaveAssetKeywords constructor.
     *
     * @param ResourceConnection $resourceConnection
     */
    public function __construct(
        ResourceConnection $resourceConnection
    ) {
        $this->resourceConnection = $resourceConnection;
    }

    /**
     * Save media assets
     *
     * @param AssetInterface $asset
     *
     * @return int
     * @throws CouldNotSaveException
     */
    public function execute(AssetInterface $asset): int
    {
        try {
            $connection = $this->resourceConnection->getConnection();
            $tableName = $this->resourceConnection->getTableName(
                self::TABLE_ADOBE_MEDIA_GALLERY
            );

            $columnsSql = $this->buildColumnsSqlPart([
                AssetInterface::PATH,
                AssetInterface::TITLE,
                AssetInterface::CONTENT_TYPE,
                AssetInterface::WIDTH,
                AssetInterface::HEIGHT,
            ]);

            $valuesSql = $this->buildValuesSqlPart();
            $onDuplicateSql = $this->buildOnDuplicateSqlPart([
                AssetInterface::PATH,
                AssetInterface::TITLE,
                AssetInterface::CONTENT_TYPE,
                AssetInterface::WIDTH,
                AssetInterface::HEIGHT,
            ]);
            $bind = $this->getSqlBindData($asset);

            $insertSql = sprintf(
                'INSERT INTO %s (%s) VALUES %s %s',
                $tableName,
                $columnsSql,
                $valuesSql,
                $onDuplicateSql
            );
            $connection->query($insertSql, $bind);

            return $this->getMediaAssetId($asset);
        } catch (\Exception $exception) {
            $message = __('An error occurred during media asset save: %1', $exception->getMessage());
            throw new CouldNotSaveException($message, $exception);
        }
    }

    /**
     * Prepare the columns sql part for the save query.
     *
     * @param array $columns
     * @return string
     */
    private function buildColumnsSqlPart(array $columns): string
    {
        $connection = $this->resourceConnection->getConnection();
        $processedColumns = array_map([$connection, 'quoteIdentifier'], $columns);
        return implode(', ', $processedColumns);
    }

    /**
     * Prepare the value sql part for the save query.
     *
     * @return string
     */
    private function buildValuesSqlPart(): string
    {
        return '(?, ?, ?, ?, ?)';
    }

    /**
     * Prepare the sql bind data for the save query.
     *
     * @param AssetInterface $asset
     * @return array
     */
    private function getSqlBindData(AssetInterface $asset): array
    {
        $bind = [];
        $bind = array_merge($bind, [
            $asset->getPath(),
            $asset->getTitle(),
            $asset->getContentType(),
            $asset->getWidth(),
            $asset->getHeight(),
        ]);

        return $bind;
    }

    /**
     * Define logic on sql duplicate.
     *
     * @param array $fields
     * @return string
     */
    private function buildOnDuplicateSqlPart(array $fields): string
    {
        $connection = $this->resourceConnection->getConnection();
        $processedFields = [];

        foreach ($fields as $field) {
            $processedFields[] = sprintf('%1$s = VALUES(%1$s)', $connection->quoteIdentifier($field));
        }
        return 'ON DUPLICATE KEY UPDATE ' . implode(', ', $processedFields);
    }

    /**
     * Get saved asset id.
     *
     * @param AssetInterface $asset
     *
     * @return int
     * @throws \Zend_Db_Statement_Exception
     */
    private function getMediaAssetId(AssetInterface $asset): int
    {
        $connection = $this->resourceConnection->getConnection();
        $select = $connection->select()
            ->from(['amg' => self::TABLE_ADOBE_MEDIA_GALLERY])
            ->where('amg.path = ?', $asset->getPath())
            ->where('amg.title = ?', $asset->getTitle())
            ->where('amg.content_type = ?', $asset->getContentType())
            ->where('amg.width = ?', $asset->getWidth())
            ->where('amg.height = ?', $asset->getHeight());

        $data = $connection->query($select)->fetchAll();

        return 1;
    }
}
