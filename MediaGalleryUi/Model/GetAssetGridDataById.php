<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Magento\MediaGalleryUi\Model;

use Magento\Framework\App\ResourceConnection;
use Magento\Framework\Exception\IntegrationException;
use Psr\Log\LoggerInterface;

/**
 * Load asset grid data selected by media asset id.
 */
class GetAssetGridDataById
{
    /**
     * Media gallery asset grid table
     */
    private const MEDIA_GALLERY_ASSET_GRID_TABLE = 'media_gallery_asset_grid';

    /**
     * Media gallery asset grid id
     */
    private const MEDIA_GALLERY_ASSET_GRID_ID = 'id';

    /**
     * @var ResourceConnection
     */
    private $resourceConnection;

    /**
     * @var LoggerInterface
     */
    private $logger;

    /**
     * GetAssetGridDataById constructor.
     *
     * @param ResourceConnection $resourceConnection
     * @param LoggerInterface $logger
     */
    public function __construct(ResourceConnection $resourceConnection, LoggerInterface $logger)
    {
        $this->resourceConnection = $resourceConnection;
        $this->logger = $logger;
    }

    /**
     * Selects data from the media grid storage filtered by asset id.
     *
     * @param int $assetId
     *
     * @return array
     * @throws IntegrationException
     */
    public function execute(int $assetId): array
    {
        try {
            $connection = $this->resourceConnection->getConnection();
            $select = $connection->select();
            $select->from($this->resourceConnection->getTableName(self::MEDIA_GALLERY_ASSET_GRID_TABLE));
            $select->where(self::MEDIA_GALLERY_ASSET_GRID_ID . ' = ?', $assetId);
            $assetGridDataById = $connection->fetchAssoc($select);

            return $assetGridDataById[$assetId] ?? [];
        } catch (\Exception $exception) {
            $this->logger->critical($exception);
            $message = __('An error occurred at getting data from the media gallery grid..');
            throw new IntegrationException($message);
        }
    }
}
