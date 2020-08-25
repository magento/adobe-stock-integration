<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 *
 */
declare(strict_types=1);

namespace Magento\MediaGalleryRenditions\Test\Integration\Model\ResourceModel;

use Magento\Framework\Exception\InvalidArgumentException;
use Magento\MediaContentApi\Api\GetAssetIdsByContentFieldInterface;
use Magento\TestFramework\Helper\Bootstrap;
use PHPUnit\Framework\TestCase;

/**
 * GetAssetIdsByContentFieldTest for MediaGalleryRenditions
 */
class GetAssetIdsByContentFieldTest extends TestCase
{
    private const STORE_FIELD = 'store_id';
    private const STATUS_FIELD = 'content_status';
    private const STATUS_ENABLED = '1';
    private const STATUS_DISABLED = '0';
    private const FIXTURE_ASSET_ID = 2020;
    private const DEFAULT_STORE_ID = '1';
    private const ADMIN_STORE_ID = '0';

    /**
     * @var GetAssetIdsByContentFieldInterface
     */
    private $getAssetIdsByContentField;

    /**
     * @inheritdoc
     */
    protected function setUp(): void
    {
        $objectManager = Bootstrap::getObjectManager();
        $this->getAssetIdsByContentField = $objectManager->get(GetAssetIdsByContentFieldInterface::class);
    }

    /**
     * Test for getting asset id by block field
     *
     * @dataProvider dataProvider
     * @magentoConfigFixture system/media_gallery/enabled 1
     * @magentoDataFixture Magento/MediaGallery/_files/media_asset.php
     * @magentoDataFixture Magento_MediaGalleryRenditions::Test/Integration/_files/cms/block_with_asset.php
     *
     * @param string $field
     * @param string $value
     * @param array $expectedAssetIds
     * @throws InvalidArgumentException
     */
    public function testBlockFields(string $field, string $value, array $expectedAssetIds): void
    {
        $this->assertEquals(
            $expectedAssetIds,
            $this->getAssetIdsByContentField->execute($field, $value)
        );
    }

    /**
     * Test for getting asset id by page field
     *
     * @dataProvider pageDataProvider
     * @magentoConfigFixture system/media_gallery/enabled 1
     * @magentoDataFixture Magento/MediaGallery/_files/media_asset.php
     * @magentoDataFixture Magento_MediaGalleryRenditions::Test/Integration/_files/cms/page_with_asset.php
     *
     * @param string $field
     * @param string $value
     * @param array $expectedAssetIds
     * @throws InvalidArgumentException
     */
    public function testPageFields(string $field, string $value, array $expectedAssetIds): void
    {
        $this->assertEquals(
            $expectedAssetIds,
            $this->getAssetIdsByContentField->execute($field, $value)
        );
    }

    /**
     * Test for getting asset id by category fields
     *
     * @dataProvider dataProvider
     * @magentoConfigFixture system/media_gallery/enabled 1
     * @magentoDataFixture Magento/MediaGallery/_files/media_asset.php
     * @magentoDataFixture Magento_MediaGalleryRenditions::Test/Integration/_files/catalog/category_with_asset.php
     *
     * @param string $field
     * @param string $value
     * @param array $expectedAssetIds
     * @throws InvalidArgumentException
     */
    public function testCategoryFields(string $field, string $value, array $expectedAssetIds): void
    {
        $this->assertEquals(
            $expectedAssetIds,
            $this->getAssetIdsByContentField->execute($field, $value)
        );
    }

    /**
     * Test for getting asset id by product fields
     *
     * @dataProvider dataProvider
     * @magentoConfigFixture system/media_gallery/enabled 1
     * @magentoDataFixture Magento/MediaGallery/_files/media_asset.php
     * @magentoDataFixture Magento_MediaGalleryRenditions::Test/Integration/_files/catalog/product_with_asset.php
     * @param string $field
     * @param string $value
     * @param array $expectedAssetIds
     * @throws InvalidArgumentException
     */
    public function testProductFields(string $field, string $value, array $expectedAssetIds): void
    {
        $this->assertEquals(
            $expectedAssetIds,
            $this->getAssetIdsByContentField->execute($field, $value)
        );
    }

    /**
     * Test for getting asset when media gallery disabled
     *
     * @magentoConfigFixture system/media_gallery/enabled 0
     * @magentoDataFixture Magento/MediaGallery/_files/media_asset.php
     * @magentoDataFixture Magento_MediaGalleryRenditions::Test/Integration/_files/catalog/product_with_asset.php
     * @throws InvalidArgumentException
     */
    public function testProductFieldsWithDisabledMediaGallery(): void
    {
        $this->assertEquals(
            [],
            $this->getAssetIdsByContentField->execute(self::STATUS_FIELD, self::STATUS_ENABLED)
        );
    }

    /**
     * Data provider for block, category and product tests
     *
     * @return array
     */
    public static function dataProvider(): array
    {
        return [
            [self::STATUS_FIELD, self::STATUS_ENABLED, [self::FIXTURE_ASSET_ID]],
            [self::STATUS_FIELD, self::STATUS_DISABLED, []],
            [self::STORE_FIELD, self::DEFAULT_STORE_ID, [self::FIXTURE_ASSET_ID]],
        ];
    }

    /**
     * Data provider for page tests
     *
     * @return array
     */
    public static function pageDataProvider(): array
    {
        return [
            [self::STATUS_FIELD, self::STATUS_ENABLED, [self::FIXTURE_ASSET_ID]],
            [self::STATUS_FIELD, self::STATUS_DISABLED, []],
            [self::STORE_FIELD, self::ADMIN_STORE_ID, [self::FIXTURE_ASSET_ID]],
        ];
    }
}
