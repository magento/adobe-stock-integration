<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Magento\AdobeStockAsset\Model\ResourceModel\Asset\Command;

use Magento\Framework\App\ResourceConnection;
use Magento\Framework\DB\Adapter\AdapterInterface;
use Magento\Framework\Exception\CouldNotDeleteException;
use Magento\AdobeStockAssetApi\Model\Asset\Command\DeleteByIdInterface;
use Psr\Log\LoggerInterface;

/**
 * Command is used to delete an Adobe Stock asset object from the data storage. Id is a filter for deletion
 */
class DeleteById implements DeleteByIdInterface
{
    private const ADOBE_STOCK_ASSET_TABLE_NAME = 'adobe_stock_asset';

    private const  ADOBE_STOCK_ASSET_ID = 'id';

    /**
     * @var ResourceConnection
     */
    private $resourceConnection;

    /**
     * @var LoggerInterface
     */
    private $logger;

    /**
     * DeleteById constructor.
     *
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
     * Delete an Adobe Stock asset filtered by id
     *
     * @param int $adobeStockAssetId
     *
     * @return void
     * @throws CouldNotDeleteException
     */
    public function execute(int $adobeStockAssetId): void
    {
        try {
            /** @var AdapterInterface $connection */
            $connection = $this->resourceConnection->getConnection();
            $tableName = $this->resourceConnection->getTableName(self::ADOBE_STOCK_ASSET_TABLE_NAME);
            $connection->delete($tableName, [self::ADOBE_STOCK_ASSET_ID . ' = ?' => $adobeStockAssetId]);
        } catch (\Exception $exception) {
            $this->logger->critical($exception);
            $message = __('Could not delete Adobe Stock asset with id %id', ['id' => $adobeStockAssetId]);
            throw new CouldNotDeleteException($message, $exception);
        }
    }
}
