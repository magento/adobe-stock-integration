<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Magento\AdobeMediaGallery\Model\Keyword\Command;

use Magento\AdobeMediaGalleryApi\Api\Data\KeywordInterface;
use Magento\AdobeMediaGalleryApi\Model\Keyword\Command\SaveAssetKeywordsInterface;
use Magento\Framework\App\ResourceConnection;
use Magento\Framework\Exception\CouldNotSaveException;

/**
 * Class SaveAssetKeywords
 */
class SaveAssetKeywords implements SaveAssetKeywordsInterface
{
    private const TABLE_KEYWORD = 'adobe_media_gallery_keyword';

    /**
     * @var ResourceConnection
     */
    private $resourceConnection;

    /**
     * SaveAssetKeywords constructor.
     *
     * @param ResourceConnection $resourceConnection
     */
    public function __construct(
        ResourceConnection $resourceConnection
    ) {
        $this->resourceConnection = $resourceConnection;
    }

    /**
     * Save asset keywords.
     *
     * @param KeywordInterface[] $keywords
     *
     * @return int[]
     * @throws CouldNotSaveException
     */
    public function execute(array $keywords): array
    {
        try {
            $values = [];
            $bind = [];
            $keywordNames = [];
            $data = [];
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
        } catch (\Exception $exception) {
            $message = __('An error occurred during save asset keyword: %1', $exception->getMessage());
            throw new CouldNotSaveException($message, $exception);
        }
    }

    /**
     * Insert ignore query
     *
     * @param string $table
     * @param array $columns
     * @param string $values
     * @param array $bind
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
     * Select keywords by names
     *
     * @param string[] $keywords
     * @return int[]
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
