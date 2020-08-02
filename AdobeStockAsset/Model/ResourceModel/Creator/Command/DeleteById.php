<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Magento\AdobeStockAsset\Model\ResourceModel\Creator\Command;

use Magento\AdobeStockAssetApi\Model\Creator\Command\DeleteByIdInterface;
use Magento\Framework\App\ResourceConnection;
use Magento\Framework\DB\Adapter\AdapterInterface;
use Magento\Framework\Exception\CouldNotDeleteException;
use Psr\Log\LoggerInterface;

/**
 * Command is used to delete an Adobe Stock asset creator object from the data storage. Id is a filter for deletion
 */
class DeleteById implements DeleteByIdInterface
{
    private const ADOBE_STOCK_ASSET_CREATOR_TABLE_NAME = 'adobe_stock_creator';

    private const  ADOBE_STOCK_ASSET_CREATOR_ID = 'id';

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
     * Delete an Adobe Stock asset creator filtered by id
     *
     * @param int $creatorId
     *
     * @return void
     * @throws CouldNotDeleteException
     */
    public function execute(int $creatorId): void
    {
        try {
            $connection = $this->resourceConnection->getConnection();
            $tableName = $this->resourceConnection->getTableName(self::ADOBE_STOCK_ASSET_CREATOR_TABLE_NAME);
            $connection->delete($tableName, [self::ADOBE_STOCK_ASSET_CREATOR_ID . ' = ?' => $creatorId]);
        } catch (\Exception $exception) {
            $this->logger->critical($exception);
            $message = __('Could not delete Adobe Stock asset creator with id %id', ['id' => $creatorId]);
            throw new CouldNotDeleteException($message, $exception);
        }
    }
}
