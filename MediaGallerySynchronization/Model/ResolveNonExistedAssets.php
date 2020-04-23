<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

declare(strict_types=1);

namespace Magento\MediaGallerySynchronization\Model;

use Magento\Framework\App\ResourceConnection;
use Psr\Log\LoggerInterface;

/**
 * Delete assets which not exist physically
 */
class ResolveNonExistedAssets
{
    private const TABLE_MEDIA_GALLERY_ASSET = 'media_gallery_asset';
    private const MEDIA_GALLERY_ASSET_PATH = 'path';

    /**
     * @var ResourceConnection
     */
    private $resourceConnection;

    /**
     * @var LoggerInterface
     */
    private $logger;

    /**
     * @param ResourceConnection $resourceConnection
     * @param LoggerInterface $logger
     */
    public function __construct(
        ResourceConnection $resourceConnection,
        LoggerInterface $logger
    ) {
        $this->resourceConnection = $resourceConnection;
        $this->logger = $logger;
    }

    /**
     * Delete assets which not existed
     *
     * @param string[] $assetsPaths
     * @return void
     */
    public function execute(array $assetsPaths): void
    {
        try {
            $connection = $this->resourceConnection->getConnection();
            $tableName = $this->resourceConnection->getTableName(self::TABLE_MEDIA_GALLERY_ASSET);
            $connection->delete($tableName, [self::MEDIA_GALLERY_ASSET_PATH . ' NOT IN (?)' => $assetsPaths]);
        } catch (\Exception $exception) {
            $this->logger->critical($exception);
        }
    }
}
