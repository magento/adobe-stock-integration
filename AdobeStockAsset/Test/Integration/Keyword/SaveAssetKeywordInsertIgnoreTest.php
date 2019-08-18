<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Magento\AdobeStockAsset\Test\Integration\Keyword;

use Magento\AdobeStockAsset\Model\ResourceModel\Keyword;
use Magento\AdobeStockAssetApi\Api\Data\KeywordInterface;
use Magento\Framework\App\ResourceConnection;
use Magento\TestFramework\Helper\Bootstrap;
use PHPUnit\Framework\TestCase;

class SaveAssetKeywordTest extends TestCase
{
    /**
     * @var Keyword
     */
    private $keywordResourceModel;

    /**
     * @var ResourceConnection
     */
    private $resourceConnection;

    /**
     * @inheritdoc
     */
    protected function setUp()
    {
        $this->keywordResourceModel = Bootstrap::getObjectManager()->get(Keyword::class);
        $this->resourceConnection = Bootstrap::getObjectManager()->get(ResourceConnection::class);
        $this->resourceConnection->getConnection()->beginTransaction();
    }

    /**
     * Rollback the test data transaction
     */
    protected function tearDown()
    {
        $this->resourceConnection->getConnection()->rollBack();
    }

    /**
     * Test save asset keyword
     *
     * @dataProvider keywordsDataProvider
     *
     * @param KeywordInterface[] $keywords
     */
    public function testSave(array $keywords)
    {
        $keywordIdsFirstSave = $this->keywordResourceModel->save($keywords);
        $keywordFirstSave = $this->getKeywordsByIds($keywordIdsFirstSave);

        $keywordIdsSecondSave = $this->keywordResourceModel->save($keywords);
        $keywordSecondSave = $this->getKeywordsByIds($keywordIdsSecondSave);

        $this->assertEquals($keywordFirstSave, $keywordSecondSave);
    }

    /**
     * Get keywords by their ids
     *
     * @param array $ids
     *
     * @return array
     */
    private function getKeywordsByIds(array $ids): array
    {
        $connection = $this->resourceConnection->getConnection();
        $select = $connection->select()
            ->from(['k' => $this->resourceConnection->getTableName($this->keywordResourceModel::TABLE_KEYWORD)])
            ->where('k.' . KeywordInterface::ID . ' in (?)', $ids);

        return $connection->fetchAll($select);
    }

    /**
     * Generate keywords for test purpose.
     *
     * @return array
     */
    public static function keywordsDataProvider(): array
    {
        $keywords =  require __DIR__ . '/../../_files/keywords.php';
        return [[$keywords]];
    }
}
