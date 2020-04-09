<?php

declare(strict_types=1);

namespace Magento\MediaGalleryUi\Model;

use Magento\Framework\App\ResourceConnection;
use Magento\Framework\Exception\IntegrationException;
use Magento\MediaGalleryUiApi\Api\GetAssetUsedInInterface;
use Psr\Log\LoggerInterface;

/**
 * @inheritDoc
 */
class GetAssetsUsedIn implements GetAssetUsedInInterface
{
    private const MEDIA_CONTENT_ASSET_TABLE_NAME = 'media_content_asset';
    private const ASSET_ID = 'asset_id';
    private const CONTENT_TYPE = 'type';

    /**
     * @var ResourceConnection
     */
    private $resourceConnection;

    /**
     * @var LoggerInterface
     */
    private $logger;

    /**
     * GetAssestsUsedIn constructor.
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
     * @inheritDoc
     */
    public function execute(int $assetId, string $contentType): int
    {
        try {
            $connection = $this->resourceConnection->getConnection();
            $select = $connection->select()
                ->from($this->resourceConnection->getTableName(self::MEDIA_CONTENT_ASSET_TABLE_NAME))
                ->where(self::ASSET_ID . '= ?', $assetId)
                ->where(self::CONTENT_TYPE . ' = ?', $contentType);
            return count($connection->fetchAll($select));
        } catch (\Exception $exception) {
            $this->logger->critical($exception);
            $message = __('An error occurred at getting media asset to content relation by media asset id.');
            throw new IntegrationException($message);
        }
    }
}
