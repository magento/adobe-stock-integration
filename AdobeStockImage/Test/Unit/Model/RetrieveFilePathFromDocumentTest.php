<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Magento\AdobeStockImage\Test\Unit\Model;

use Magento\AdobeStockImage\Model\RetrieveFilePathFromDocument;
use Magento\AdobeStockImage\Model\Storage\Delete as StorageDelete;
use Magento\AdobeStockImage\Model\Storage\Save as StorageSave;
use Magento\Framework\Api\AttributeInterface;
use Magento\Framework\Api\Search\Document;
use Magento\Framework\TestFramework\Unit\Helper\ObjectManager;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

/**
 * Test saving image file and create Media Gallery asset.
 */
class RetrieveFilePathFromDocumentTest extends TestCase
{
    /**
     * @var MockObject|StorageSave
     */
    private $storageSave;

    /**
     * @var MockObject|StorageDelete
     */
    private $storageDelete;

    /**
     * @var RetrieveFilePathFromDocument
     */
    private $retrieveFilePathFromDocument;

    /**
     * @inheritdoc
     */
    protected function setUp(): void
    {
        $this->storageSave = $this->createMock(StorageSave::class);
        $this->storageDelete = $this->createMock(StorageDelete::class);

        $this->retrieveFilePathFromDocument = (new ObjectManager($this))->getObject(
            RetrieveFilePathFromDocument::class,
            [
                'storageSave' => $this->storageSave,
                'storageDelete' => $this->storageDelete
            ]
        );
    }

    /**
     * Verify getting path from document aster image save.
     *
     * @param Document $document
     * @param string $url
     * @param string $destinationPath
     * @param bool $delete
     * @dataProvider assetProvider
     */
    public function testExecute(Document $document, string $url, string $destinationPath, bool $delete): void
    {
        $path = 'catalog/test-image.jpeg';
        if ($delete) {
            $this->storageDelete->expects($this->once())
                ->method('execute');
        } else {
            $this->storageDelete->expects($this->never())
                ->method('execute');
        }

        $this->storageSave->expects($this->once())
            ->method('execute')
            ->with($url, $destinationPath)
            ->willReturn($path);

        $this->retrieveFilePathFromDocument->execute($document, $url, $destinationPath);
    }

    /**
     * Data provider for testExecute
     *
     * @return array
     */
    public function assetProvider(): array
    {
        return [
            [
                'document' => $this->getDocument(),
                'url' => 'https://as2.ftcdn.net/jpg/500_FemVonDcttCeKiOXFk.jpg',
                'destinationPath' => 'path',
                'delete' => false
            ],
            [
                'document' => $this->getDocument('filepath.jpg'),
                'url' => 'https://as2.ftcdn.net/jpg/500_FemVonDcttCeKiOXFk.jpg',
                'destinationPath' => 'path',
                'delete' => true
            ],
        ];
    }

    /**
     * Get document
     *
     * @param string|null $path
     * @return MockObject
     */
    private function getDocument(?string $path = null): MockObject
    {
        $document = $this->createMock(Document::class);
        $pathAttribute = $this->createMock(AttributeInterface::class);
        $pathAttribute->expects($this->once())
            ->method('getValue')
            ->willReturn($path);
        $document->expects($this->once())
            ->method('getCustomAttribute')
            ->with('path')
            ->willReturn($pathAttribute);

        return $document;
    }
}
