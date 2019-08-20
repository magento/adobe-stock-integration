<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Magento\AdobeStockAsset\Test\Integration;

use Magento\AdobeStockAssetApi\Api\Data\AssetInterface;
use Magento\AdobeStockAssetApi\Api\AssetRepositoryInterface;
use Magento\AdobeStockAssetApi\Api\AssetKeywordRepositoryInterface;
use Magento\AdobeStockAssetApi\Api\Data\KeywordInterface;
use Magento\TestFramework\Helper\Bootstrap;
use PHPUnit\Framework\TestCase;

/**
 * Class GetAssetKeywordByAssetIdtest
 */
class GetAssetKeywordByAssetIdTest extends TestCase
{
    /**
     * @var AssetKeywordRepositoryInterface
     */
    private $assetKeywordsRepository;

    /**
     * @inheritdoc
     */
    protected function setUp()
    {
        $this->assetKeywordsRepository = Bootstrap::getObjectManager()->get(AssetKeywordRepositoryInterface::class);
    }

    /**
     * Rollback the test data transaction
     */
    protected function tearDown()
    {
        require __DIR__ . '/../_files/asset_rollback.php';
    }

    /**
     * @dataProvider testGetAssetKeywordByIdDataProvider
     */
    public function testGetAssetKeywordById(AssetInterface $asset)
    {
        $savedData = [];
        $keywords = $this->assetKeywordsRepository->getAssetKeywords($asset);
        /** @var KeywordInterface $saved */
        foreach ($keywords as $saved) {
            $savedData[] = $saved->getKeyword();
        }

        $originalData = [];
        $originalKeywords = $asset->getKeywords();
        /** @var KeywordInterface $original */
        foreach ($originalKeywords as $original) {
            $originalData[] = $original->getKeyword();
        }

        $result = !array_diff($originalData, $savedData);
        $this->assertTrue($result);
    }

    /**
     * @return AssetInterface[]
     */
    public static function testGetAssetKeywordByIdDataProvider(): array
    {
        /** @var AssetInterface $asset */
        $asset = require __DIR__ . '/../_files/asset.php';

        return [[$asset]];
    }
}
