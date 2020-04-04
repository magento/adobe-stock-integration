<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Magento\AdobeStockAsset\Model\ResourceModel\Asset\Command;

use Magento\AdobeStockAssetApi\Api\Data\AssetInterface;
use Magento\AdobeStockAssetApi\Model\Asset\Command\SaveInterface;
use Magento\Framework\App\ResourceConnection;
use Magento\Framework\Exception\CouldNotSaveException;
use Magento\Framework\Reflection\DataObjectProcessor;
use Psr\Log\LoggerInterface;

/**
 * Command is used to safe an Adobe Stock asset data to the data storage
 */
class Save implements SaveInterface
{
    private const ADOBE_STOCK_ASSET_TABLE_NAME = 'adobe_stock_asset';

    /**
     * @var ResourceConnection
     */
    private $resourceConnection;

    /**
     * @var DataObjectProcessor
     */
    private $objectProcessor;

    /**
     * @var LoggerInterface
     */
    private $logger;

    /**
     * Save constructor.
     *
     * @param ResourceConnection $resourceConnection
     * @param DataObjectProcessor $objectProcessor
     * @param LoggerInterface $logger
     */
    public function __construct(
        ResourceConnection $resourceConnection,
        DataObjectProcessor $objectProcessor,
        LoggerInterface $logger
    ) {
        $this->resourceConnection = $resourceConnection;
        $this->objectProcessor = $objectProcessor;
        $this->logger = $logger;
    }

    /**
     * Save an Adobe Stock asset.
     *
     * @param AssetInterface $asset
     *
     * @throws CouldNotSaveException
     */
    public function execute(AssetInterface $asset): void
    {
        try {
            $data = $this->objectProcessor->buildOutputDataArray($asset, AssetInterface::class);

            if (empty($data)) {
                return;
            }

            $connection = $this->resourceConnection->getConnection();
            $tableName = $this->resourceConnection->getTableName(self::ADOBE_STOCK_ASSET_TABLE_NAME);
            $saveData = $this->filterData($data, array_keys($connection->describeTable($tableName)));
            $connection->insertOnDuplicate($tableName, $saveData);
        } catch (\Exception $exception) {
            $this->logger->critical($exception);
            $message = __(
                'An error occurred during adobe stock asset save: %error',
                ['error' => $exception->getMessage()]
            );
            throw new CouldNotSaveException($message, $exception);
        }
    }

    /**
     * Filter data to keep only data for columns specified
     *
     * @param array $data
     * @param array $columns
     * @return array
     */
    private function filterData(array $data, array $columns): array
    {
        return array_intersect_key($data, array_flip($columns));
    }
}
