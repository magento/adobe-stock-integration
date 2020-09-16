<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

declare(strict_types=1);

namespace Magento\AdobeStockImage\Test\Integration\Model;

use AdobeStock\Api\Models\StockFile;
use Magento\AdobeStockAssetApi\Api\AssetRepositoryInterface;
use Magento\AdobeStockClient\Model\StockFileToDocument;
use Magento\AdobeStockImage\Model\SaveImageFile;
use Magento\AdobeStockImage\Model\Storage\Save;
use Magento\AdobeStockImageApi\Api\SaveImageInterface;
use Magento\Framework\Api\AttributeValueFactory;
use Magento\Framework\Api\Search\Document;
use Magento\Framework\Api\SearchCriteriaBuilder;
use Magento\Framework\App\Filesystem\DirectoryList;
use Magento\Framework\Exception\IntegrationException;
use Magento\Framework\Exception\LocalizedException;
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
     * @var string
     */
    private $saveDestination = 'catalog/category/tmp.png';

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
    private $searchCriteriaBuilder;

    /**
     * @inheritdoc
     */
    protected function setUp(): void
    {
        $this->driver = Bootstrap::getObjectManager()->get(DriverInterface::class);
        $this->fileSystem = Bootstrap::getObjectManager()->get(Filesystem::class);
        $this->assetRepository = Bootstrap::getObjectManager()->get(AssetRepositoryInterface::class);
        $this->searchCriteriaBuilder = Bootstrap::getObjectManager()->get(SearchCriteriaBuilder::class);

        $this->deleteImage();
        $https = $this->createMock(Https::class);
        $https->expects($this->once())
            ->method('fileGetContents')
            ->willReturnCallback(function ($filePath) {
                return file_get_contents($filePath);
            });
        $storageSave = Bootstrap::getObjectManager()->create(Save::class, ['driver' => $https,]);
        $saveImageFile = Bootstrap::getObjectManager()->create(SaveImageFile::class, ['storageSave' => $storageSave,]);
        $this->saveImage = Bootstrap::getObjectManager()->create(
            SaveImageInterface::class,
            ['saveImageFile' => $saveImageFile]
        );
    }

    /**
     * @inheridoc
     */
    protected function tearDown(): void
    {
        $this->deleteImage();
        parent::tearDown();
    }

    /**
     * Test with image.
     *
     * @return void
     */
    public function testSave(): void
    {
        $document = $this->getDocument();
        $this->saveImage->execute(
            $document,
            $document->getCustomAttribute(self::URL_FIELD)->getValue(),
            $this->fileSystem->getDirectoryWrite(DirectoryList::MEDIA)->getAbsolutePath($this->saveDestination)
        );
        self::assertTrue(
            $this->fileSystem->getDirectoryRead(DirectoryList::MEDIA)->isExist($this->saveDestination),
            'File was not saved by destination'
        );
        $searchCriteria = $this->searchCriteriaBuilder
            ->addFilter('creator_id', $document->getCustomAttribute('creator_id')->getValue(), 'eq')
            ->create();
        self::assertNotEmpty(
            $this->assetRepository->getList($searchCriteria),
            'Image asset was not saved'
        );
    }

    /**
     * Document for save.
     *
     * @return Document
     * @throws IntegrationException
     */
    private function getDocument(): Document
    {
        $stockFileData = [
            'id' => 1,
            'comp_url' => 'https://test.url/1.png',
            'width' => 110,
            'title' => 'test',
            'content_type' => 'image/png',
            'height' => 210,
            'some_bool_param' => false,
            'some_nullable_param' => null,
            'category' => [
                'id' => 1,
                'name' => 'Test'
            ],
        ];

        $stockFile = new StockFile($stockFileData);
        /** @var StockFileToDocument $stockFileToDocument */
        $stockFileToDocument = Bootstrap::getObjectManager()->create(StockFileToDocument::class);
        $document = $stockFileToDocument->convert($stockFile);
        $this->addAttributes($document, [
            'is_downloaded' => 0,
            'path' => '',
            'is_licensed_locally' => 0,
            self::URL_FIELD => $this->getImageFilePath('magento-logo.png'),
            'creator_id' => 1122,
            'creator_name' => 'Test'
        ]);
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
     * @return void
     */
    private function deleteImage(): void
    {
        $mediaDir = $this->fileSystem->getDirectoryWrite(DirectoryList::MEDIA);
        if ($mediaDir->isExist($this->saveDestination)) {

            $this->driver->deleteFile($mediaDir->getAbsolutePath($this->saveDestination));
        }
    }
}
