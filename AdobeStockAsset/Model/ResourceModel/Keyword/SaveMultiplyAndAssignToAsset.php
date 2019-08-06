<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Magento\AdobeStockAsset\Model\ResourceModel\Keyword;

use Magento\AdobeStockAssetApi\Api\Data\AssetInterface;
use Magento\AdobeStockAssetApi\Api\Data\KeywordInterface;
use Magento\AdobeStockAsset\Model\ResourceModel\Keyword as KeywordResourceModel;
use Magento\Framework\App\ResourceConnection;
use Magento\Framework\Exception\CouldNotSaveException;
use Psr\Log\LoggerInterface;

/**
 * Class SaveMultiplyAndAssignToAsset
 */
class SaveMultiplyAndAssignToAsset
{
    /**
     * @var ResourceConnection
     */
    private $resourceConnection;

    /**
     * @var SaveMultiple
     */
    private $saveMultiple;

    /**
     * @var LoggerInterface
     */
    private $logger;

    /**
     * SaveMultiplyAndAssignToAsset constructor.
     *
     * @param ResourceConnection $resourceConnection
     * @param SaveMultiple       $saveMultiple
     * @param LoggerInterface    $logger
     */
    public function __construct(
        ResourceConnection $resourceConnection,
        SaveMultiple $saveMultiple,
        LoggerInterface $logger
    ) {
        $this->resourceConnection = $resourceConnection;
        $this->saveMultiple = $saveMultiple;
        $this->logger = $logger;
    }

    /**
     * Save keywords and assigned to asset.
     *
     * @param AssetInterface $asset
     * @return void
     *
     * @throws CouldNotSaveException
     */
    public function execute(AssetInterface $asset): void
    {
        try {
            $keywords = $asset->getKeywords();
            $assignedKeywords = $this->getAlreadyAssignedKeywords($asset);
            /** @var KeywordInterface $keywordValue */
            foreach ($assignedKeywords as $assignedKey => $assignedData) {
                foreach ($keywords as $keywordKey => $keywordValue) {
                    if ($assignedData[KeywordInterface::KEYWORD] === $keywordValue->getKeyword()) {
                        unset($keywords[$keywordKey]);
                    }
                }
            }
            $this->saveMultiple->execute($keywords);
            $this->assignKeywordsToAsset($asset, $keywords);
        } catch (\Exception $exception) {
            $message = __('Save keywords and assign them to asset failed: %1', $exception->getMessage());
            $this->logger->critical($message);
            throw new CouldNotSaveException($message, $exception);
        }
    }

    /**
     * Assign keywords to asset.
     *
     * @param AssetInterface $asset
     * @param array          $keywords
     */
    private function assignKeywordsToAsset(AssetInterface $asset, array $keywords)
    {
        if (!empty($keywords)) {
            $keywordIds = $this->selectKeywordsIdsByNames($keywords);
            $assetKeywordData = [];
            foreach ($keywordIds as $keywordId) {
                $assetKeywordData[] = [
                    KeywordInterface::ASSET_KEYWORD_ASSET_ID => $asset->getId(),
                    KeywordInterface::ASSET_KEYWORD_KEYWORD_ID => $keywordId,
                ];
            }

            if (!empty($assetKeywordData)) {
                $this->resourceConnection->getConnection()->insertMultiple(
                    $this->resourceConnection->getTableName(KeywordResourceModel::ADOBE_STOCK_ASSET_KEYWORD_TABLE_NAME),
                    $assetKeywordData
                );
            }
        }
    }

    /**
     * Select keywords already assign to the current asset.
     *
     * @param AssetInterface $asset
     *
     * @return array
     */
    private function getAlreadyAssignedKeywords(AssetInterface $asset): array
    {
        $keywordNames = [];
        $keywords = $asset->getKeywords();
        /** @var KeywordInterface $keyword */
        foreach ($keywords as $keyword) {
            $keywordNames[] = $keyword->getKeyword();
        }
        $connection = $this->resourceConnection->getConnection();
        $select = $connection->select()
            ->from(
                [
                    'adobe_stock_keyword' => $this->resourceConnection
                        ->getTableName(KeywordResourceModel::ADOBE_STOCK_KEYWORD_TABLE_NAME),
                ]
            )
            ->join(
                [
                    'adobe_stock_asset_keyword' => $this->resourceConnection
                        ->getTableName(KeywordResourceModel::ADOBE_STOCK_ASSET_KEYWORD_TABLE_NAME),
                ],
                'adobe_stock_asset_keyword.' . KeywordInterface::ASSET_KEYWORD_KEYWORD_ID . '
                = adobe_stock_keyword.' . KeywordInterface::ID,
                []
            )
            ->where('adobe_stock_keyword.' . KeywordInterface::KEYWORD . ' in (?)', $keywordNames);

        $result = $connection->fetchAll($select);

        return $result;
    }

    /**
     * Select keywords by names.
     *
     * @param KeywordInterface[] $keywords
     *
     * @return array
     */
    private function selectKeywordsIdsByNames(array $keywords): array
    {
        $keywordNames = [];
        /** @var KeywordInterface $keyword */
        foreach ($keywords as $keyword) {
            $keywordNames[] = $keyword->getKeyword();
        }
        $connection = $this->resourceConnection->getConnection();
        $select = $connection->select()
            ->from(
                [
                    'adobe_stock_keyword' => $this->resourceConnection->getTableName(
                        KeywordResourceModel::ADOBE_STOCK_KEYWORD_TABLE_NAME
                    ),
                ]
            )
            ->columns(KeywordInterface::ID)
            ->where(
                'adobe_stock_keyword.' . KeywordInterface::KEYWORD . ' in (?)',
                $keywordNames
            );

        $result = $connection->fetchCol($select);

        return $result;
    }
}
