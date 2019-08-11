<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

declare(strict_types=1);

namespace Magento\AdobeStockAsset\Model\ResourceModel;

use Magento\AdobeStockAssetApi\Api\Data\KeywordInterface;
use Magento\AdobeStockAssetApi\Api\Data\KeywordInterfaceFactory;
use Magento\Framework\App\ResourceConnection;

/**
 * Asset (metadata) resource model
 */
class Keyword
{
    const TABLE_ASSET_KEYWORD = 'adobe_stock_asset_keyword';
    const TABLE_KEYWORD = 'adobe_stock_keyword';

    const FIELD_ASSET_ID = 'asset_id';
    const FIELD_KEYWORD_ID = 'keyword_id';

    /**
     * @var ResourceConnection
     */
    private $resourceConnection;

    /**
     * @var KeywordInterfaceFactory
     */
    private $keywordFactory;

    /**
     * Keyword constructor.
     * @param KeywordInterfaceFactory $keywordFactory
     * @param ResourceConnection $resourceConnection
     */
    public function __construct(
        KeywordInterfaceFactory $keywordFactory,
        ResourceConnection $resourceConnection
    ) {
        $this->resourceConnection = $resourceConnection;
        $this->keywordFactory = $keywordFactory;
    }

    /**
     * @return string
     */
    public function getIdFieldName()
    {
        return KeywordInterface::ID;
    }

    /**
     * @param int $assetId
     * @param \int[] $keywordIds
     */
    public function saveAssetLinks(int $assetId, array $keywordIds): void
    {
        if (empty($keywordIds)) {
            return;
        }

        $values = [];
        $bind = [];
        foreach ($keywordIds as $keywordId) {
            $values[] = sprintf('(%s)', implode(',', ['?', '?']));
            $bind[] = $assetId;
            $bind[] = $keywordId;
        }

        $this->insertIgnore(
            self::TABLE_ASSET_KEYWORD,
            [self::FIELD_ASSET_ID, self::FIELD_KEYWORD_ID],
            implode(',', $values),
            $bind
        );
    }

    /**
     * @param KeywordInterface[] $keyword
     * @return \int[]
     */
    public function save(array $keywords): array
    {
        $values = [];
        $bind = [];
        $keywordNames = [];
        /** @var KeywordInterface $keyword */
        foreach ($keywords as $keyword) {
            $keywordNames[] = $keyword->getKeyword();
            $data[KeywordInterface::ID] = $keyword->getId();
            $data[KeywordInterface::KEYWORD] = $keyword->getKeyword();
            $values[] = sprintf('(%s)', implode(',', array_pad([], count($data), '?')));
            foreach ($data as $value) {
                $bind[] = $value;
            }
        }

        $this->insertIgnore(
            self::TABLE_KEYWORD,
            array_keys($data),
            implode(',', $values),
            $bind
        );

        return $this->getKeywordIds($keywordNames);
    }

    /**
     * @param int $assetId
     * @return KeywordInterface[]
     * @throws \Zend_Db_Statement_Exception
     */
    public function loadByAssetId(int $assetId): array
    {
        $connection = $this->resourceConnection->getConnection();

        $select = $connection->select()
            ->from(['k' => self::TABLE_KEYWORD])
            ->join(['ak' => self::TABLE_ASSET_KEYWORD], 'k.id = ak.keyword_id')
            ->where('ak.asset_id = ?', $assetId);
        $data = $connection->query($select)->fetchAll();

        $keywords = [];
        foreach ($data as $keywordData) {
            $keyword = $this->keywordFactory->create();
            $keyword->setId($keywordData[KeywordInterface::ID]);
            $keyword->setKeyword($keywordData[KeywordInterface::KEYWORD]);
            $keywords[] = $keyword;
        }

        return $keywords;
    }

    /**
     * @param string $table
     * @param array $data
     */
    private function insertIgnore(string $table, array $columns, string $values, array $bind): void
    {
        $connection = $this->resourceConnection->getConnection();

        $connection->query(
            sprintf(
                'INSERT IGNORE INTO %s (%s) VALUES %s',
                $connection->quoteIdentifier($this->resourceConnection->getTableName($table)),
                join(',', array_map([$connection, 'quoteIdentifier'], $columns)),
                $values
            ),
            $bind
        );
    }

    /**
     * Select keywords by names.
     *
     * @param \string[] $keywords
     * @return \int[]
     */
    private function getKeywordIds(array $keywords): array
    {
        $connection = $this->resourceConnection->getConnection();
        $select = $connection->select()
            ->from(['k' => $this->resourceConnection->getTableName(self::TABLE_KEYWORD)])
            ->columns(KeywordInterface::ID)
            ->where('k.' . KeywordInterface::KEYWORD . ' in (?)', $keywords);

        return $connection->fetchCol($select);
    }
}
