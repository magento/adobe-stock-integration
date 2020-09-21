<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

declare(strict_types=1);

namespace Magento\AdobeStockImage\Test\Integration\Model;

use Magento\AdobeStockAssetApi\Api\AssetRepositoryInterface;
use Magento\AdobeStockImageApi\Api\SaveImageInterface;
use Magento\Framework\Api\AttributeValueFactory;
use Magento\Framework\Api\Search\Document;
use Magento\Framework\Api\SearchCriteriaBuilder;
use Magento\Framework\App\Filesystem\DirectoryList;
use Magento\Framework\Filesystem;
use Magento\Framework\Filesystem\Driver\Https;
use Magento\MediaGalleryApi\Api\GetAssetsByPathsInterface;
use Magento\TestFramework\Helper\Bootstrap;
use PHPUnit\Framework\TestCase;

/**
 * Test client for communication to Adobe Stock API.
 */
class SaveImageTest extends TestCase
{
    /**
     * @var Filesystem
     */
    private $fileSystem;

    /**
     * @inheritdoc
     */
    protected function setUp(): void
    {
        $this->fileSystem = Bootstrap::getObjectManager()->get(Filesystem::class);
        Bootstrap::getObjectManager()->configure([
            'preferences' => [
                Https::class => HttpsDriverMock::class
            ]
        ]);
    }

    /**
     * Test with image.
     *
     * @param array $documentData
     * @param string $sourceFile
     * @param string $destinationPath
     * @return void
     * @dataProvider getSaveTestDataProvider
     */
    public function testSave(array $documentData, string $sourceFile, string $destinationPath): void
    {
        $this->deleteImage($destinationPath);
        $document = $this->getDocument($documentData);
        $saveImage = Bootstrap::getObjectManager()->create(SaveImageInterface::class);
        $saveImage->execute(
            $document,
            $this->getImageFilePath($sourceFile),
            $destinationPath
        );
        $this->assertImageSavedToDirectory($destinationPath);
        $this->assertAssets($destinationPath, $documentData);
        $this->deleteImage($destinationPath);
    }

    /**
     * @return array
     */
    public function getSaveTestDataProvider(): array
    {
        return [
            'image_save' => [
                'documentData' => [
                    'id' => 1,
                    'comp_url' => 'https://test.url/magento-logo.png',
                    'width' => 110,
                    'title' => 'test adobe image title',
                    'content_type' => 'image/png',
                    'height' => 210,
                    'some_bool_param' => false,
                    'some_nullable_param' => null,
                    'extension_attributes' => [
                        'title' => 'test adobe image title',
                        'is_downloaded' => 0,
                        'is_licensed_locally' => 0,
                        'thumbnail_240_url' => 'https://test.url/magento-logo.png',
                        'creator_id' => random_int(0, 2147483647),
                        'creator_name' => 'Test',
                        'path' => 'catalog/category/tmp.png',
                        'content_type' => 'image/png',
                        'category' => [
                            'id' => random_int(0, 2147483647),
                            'name' => 'Test'
                        ],
                    ]
                ],
                'sourcePath' => 'magento-logo.png',
                'destinationPath' => 'catalog/category/adobe-stock-save-image-test.png',
            ]
        ];
    }

    /**
     * Document for save.
     *
     * @param array $documentData
     * @return Document
     */
    private function getDocument(array $documentData): Document
    {
        $document = new Document($documentData);
        $this->addAttributes($document, $documentData['extension_attributes']);
        return $document;
    }

    /**
     * Check if image saved by destination path
     *
     * @param string $destinationPath
     * @return void
     */
    private function assertImageSavedToDirectory(string $destinationPath): void
    {
        self::assertTrue(
            $this->fileSystem->getDirectoryRead(DirectoryList::MEDIA)->isExist($destinationPath),
            'File was not saved by destination'
        );
    }

    /**
     * Assert saved assets data
     *
     * @param string $destinationPath
     * @param array $documentData
     * @return void
     */
    private function assertAssets(string $destinationPath, array $documentData): void
    {
        $galleryAssets = Bootstrap::getObjectManager()->get(GetAssetsByPathsInterface::class);
        $mediaAssets = $galleryAssets->execute([$destinationPath]);
        self::assertCount(1, $mediaAssets, 'Wrong gallery assets count');
        self::assertEquals(
            $documentData['extension_attributes']['title'],
            $mediaAssets[0]->getTitle(),
            'Wrong gallery assets image title saved'
        );
        $criteriaBuilder = Bootstrap::getObjectManager()->get(SearchCriteriaBuilder::class);
        $searchCriteria = $criteriaBuilder
            ->addFilter('media_gallery_id', $mediaAssets[0]->getId())
            ->create();
        /** @var AssetRepositoryInterface $stockAssets */
        $stockAssets = Bootstrap::getObjectManager()->get(AssetRepositoryInterface::class);
        $items = $stockAssets->getList($searchCriteria)->getItems();
        self::assertNotEmpty(
            $items,
            'Image asset was not saved'
        );
        $item = reset($items);
        self::assertEquals(
            $documentData['extension_attributes']['creator_id'],
            $item->getCreatorId(),
            'Wrong stock asset creator id saved'
        );
        self::assertEquals(
            $documentData['extension_attributes']['category']['id'],
            $item->getCategoryId(),
            'Wrong stock asset category id saved'
        );
    }

    /**
     * Add attributes to document
     *
     * @param Document $document
     * @param array $attributes [code => value]
     * @return Document
     */
    private function addAttributes(Document $document, array $attributes): Document
    {
        $customAttributes = $document->getCustomAttributes();
        $valueFactory = Bootstrap::getObjectManager()->create(
            AttributeValueFactory::class
        );
        foreach ($attributes as $code => $value) {
            $attribute = $valueFactory->create();
            $attribute->setAttributeCode($code);
            $attribute->setValue($value);
            $customAttributes[$code] = $attribute;
        }

        $document->setCustomAttributes($customAttributes);

        return $document;
    }

    /**
     * Return image file path
     *
     * @param string $filename
     * @return string
     */
    private function getImageFilePath(string $filename): string
    {
        return implode(
            DIRECTORY_SEPARATOR,
            [
                dirname(__DIR__, 1),
                '_files',
                $filename
            ]
        );
    }

    /**
     * Delete test image if exists
     *
     * @param string $destinationPath
     * @return void
     */
    private function deleteImage(string $destinationPath): void
    {
        $this->fileSystem->getDirectoryWrite(DirectoryList::MEDIA)->delete($destinationPath);
    }
}
