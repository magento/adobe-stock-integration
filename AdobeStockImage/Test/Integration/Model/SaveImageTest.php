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
use Magento\Framework\Exception\IntegrationException;
use Magento\Framework\Filesystem;
use Magento\Framework\Filesystem\Driver\Https;
use Magento\Framework\Filesystem\DriverInterface;
use Magento\TestFramework\Helper\Bootstrap;
use PHPUnit\Framework\TestCase;

/**
 * Test client for communication to Adobe Stock API.
 */
class SaveImageTest extends TestCase
{
    const URL_FIELD = 'thumbnail_240_url';

    /**
     * @var SaveImageInterface
     */
    private $saveImage;

    /**
     * @var DriverInterface
     */
    private $driver;

    /**
     * @var Filesystem
     */
    private $fileSystem;

    /**
     * @var AssetRepositoryInterface
     */
    private $assetRepository;

    /**
     * @var SearchCriteriaBuilder
     */
    private $criteriaBuilder;

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
                    'title' => 'test',
                    'content_type' => 'image/png',
                    'height' => 210,
                    'some_bool_param' => false,
                    'some_nullable_param' => null,
                    'extension_attributes' => [
                        'title' => 'test',
                        'is_downloaded' => 0,
                        'is_licensed_locally' => 0,
                        'thumbnail_240_url' => 'https://test.url/magento-logo.png',
                        'creator_id' => 1122,
                        'creator_name' => 'Test',
                        'path' => 'catalog/category/tmp.png',
                        'content_type' => 'image/png',
                        'category' => [
                            'id' => 1,
                            'name' => 'Test'
                        ],
                    ]
                ],
                'sourcePath' => 'magento-logo.png',
                'destinationPath' => 'catalog/category/tmp.png',
            ]
        ];
    }

    /**
     * @inheritdoc
     */
    protected function setUp(): void
    {
        $this->driver = Bootstrap::getObjectManager()->get(DriverInterface::class);
        $this->fileSystem = Bootstrap::getObjectManager()->get(Filesystem::class);
        $this->assetRepository = Bootstrap::getObjectManager()->get(AssetRepositoryInterface::class);
        $this->criteriaBuilder = Bootstrap::getObjectManager()->get(SearchCriteriaBuilder::class);
        Bootstrap::getObjectManager()->configure([
            'preferences' => [
                Https::class => HttpsDriverMock::class
            ]
        ]);
        $this->saveImage = Bootstrap::getObjectManager()->create(SaveImageInterface::class);
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
        $mediaDir = $this->fileSystem->getDirectoryWrite(DirectoryList::MEDIA);
        $this->saveImage->execute(
            $document,
            $this->getImageFilePath($sourceFile),
            $mediaDir->getAbsolutePath($destinationPath)
        );
        self::assertTrue(
            $this->fileSystem->getDirectoryRead(DirectoryList::MEDIA)->isExist($destinationPath),
            'File was not saved by destination'
        );
        $searchCriteria = $this->criteriaBuilder
            ->addFilter('creator_id', $document->getCustomAttribute('creator_id')->getValue(), 'eq')
            ->create();
        self::assertNotEmpty(
            $this->assetRepository->getList($searchCriteria),
            'Image asset was not saved'
        );
        $this->deleteImage($destinationPath);
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
     * Add attributes to document
     *
     * @param Document $document
     * @param array $attributes [code => value]
     * @return Document
     */
    private function addAttributes(Document $document, array $attributes): Document
    {
        $customAttributes = $document->getCustomAttributes();
        $attributeValueFactory = Bootstrap::getObjectManager()->create(
            AttributeValueFactory::class
        );
        foreach ($attributes as $code => $value) {
            $attribute = $attributeValueFactory->create();
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
        return dirname(__DIR__, 1)
            . DIRECTORY_SEPARATOR
            . implode(
                DIRECTORY_SEPARATOR,
                [
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
        $mediaDir = $this->fileSystem->getDirectoryWrite(DirectoryList::MEDIA);
        if ($mediaDir->isExist($destinationPath)) {
            $this->driver->deleteFile($mediaDir->getAbsolutePath($destinationPath));
        }
    }
}
