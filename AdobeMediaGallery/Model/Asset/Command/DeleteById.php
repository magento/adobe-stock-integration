<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Magento\AdobeMediaGallery\Model\Asset\Command;

use Magento\AdobeMediaGalleryApi\Api\Data\AssetInterface;
use Magento\AdobeMediaGalleryApi\Model\Asset\Command\DeleteByIdInterface;
use Magento\AdobeMediaGalleryApi\Model\Asset\Command\GetByIdInterface;
use Magento\Framework\App\ResourceConnection;
use Magento\Framework\Exception\CouldNotDeleteException;
use Magento\Framework\DB\Adapter\AdapterInterface;
use Psr\Log\LoggerInterface;

/**
 * Class DeleteById
 */
class DeleteById implements DeleteByIdInterface
{
    private const TABLE_MEDIA_GALLERY_ASSET = 'media_gallery_asset';

    /**
     * @var ResourceConnection
     */
    private $resourceConnection;

    /**
     * @var GetByIdInterface
     */
    private $getMediaAssetById;

    /**
     * @var LoggerInterface
     */
    private $logger;

    /**
     * DeleteById constructor.
     *
     * @param ResourceConnection $resourceConnection
     * @param GetByIdInterface $getMediaAssetById
     * @param LoggerInterface $logger
     */
    public function __construct(
        ResourceConnection $resourceConnection,
        GetByIdInterface $getMediaAssetById,
        LoggerInterface $logger
    ) {
        $this->resourceConnection = $resourceConnection;
        $this->getMediaAssetById = $getMediaAssetById;
        $this->logger = $logger;
    }

    /**
     * Delete media asset by id
     *
     * @param int $mediaAssetId
     *
     * @return void
     * @throws CouldNotDeleteException
     */
    public function execute(int $mediaAssetId): void
    {
        try {
            /** @var AssetInterface $mediaAsset */
            $mediaAsset = $this->getMediaAssetById->execute($mediaAssetId);
            /** @var AdapterInterface $connection */
            $connection = $this->resourceConnection->getConnection();
            $tableName = $this->resourceConnection->getTableName(self::TABLE_MEDIA_GALLERY_ASSET);
            $connection->delete($tableName, 'id = ' . $mediaAsset->getId());
        } catch (\Exception $exception) {
            $message = __(
                'Could not delete media asset with id %id: %error',
                ['id' => $mediaAssetId, 'error' => $exception->getMessage()]
            );
            $this->logger->critical($message);
            throw new CouldNotDeleteException($message, $exception);
        }
    }
}
