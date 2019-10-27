<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Magento\AdobeMediaGallery\Model\Asset\Command;

use Magento\AdobeMediaGallery\Model\DataExtractor;
use Magento\AdobeMediaGalleryApi\Api\Data\AssetInterface;
use Magento\AdobeMediaGalleryApi\Model\Asset\Command\SaveInterface;
use Magento\Framework\App\ResourceConnection;
use Magento\Framework\Exception\CouldNotSaveException;

/**
 * Class Save
 */
class Save implements SaveInterface
{
    private const TABLE_MEDIA_GALLERY_ASSET = 'media_gallery_asset';

    /**
     * @var ResourceConnection
     */
    private $resourceConnection;

    /**
     * @var DataExtractor
     */
    private $extractor;

    /**
     * @param ResourceConnection $resourceConnection
     * @param DataExtractor $extractor
     */
    public function __construct(
        ResourceConnection $resourceConnection,
        DataExtractor $extractor
    ) {
        $this->resourceConnection = $resourceConnection;
        $this->extractor = $extractor;
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
            /** @var \Magento\Framework\DB\Adapter\Pdo\Mysql $connection */
            $connection = $this->resourceConnection->getConnection();
            $tableName = $this->resourceConnection->getTableName(self::TABLE_MEDIA_GALLERY_ASSET);

            $connection->insertOnDuplicate($tableName, $this->extractor->extract($asset, AssetInterface::class));
            return (int) $connection->lastInsertId($tableName);
        } catch (\Exception $exception) {
            $message = __('An error occurred during media asset save: %1', $exception->getMessage());
            throw new CouldNotSaveException($message, $exception);
        }
    }
}
