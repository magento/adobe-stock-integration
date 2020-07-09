<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Magento\MediaGalleryMetadata\Test\Integration\Model;

use Magento\Framework\App\Filesystem\DirectoryList;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Filesystem;
use Magento\Framework\Filesystem\Directory\WriteInterface;
use Magento\Framework\Filesystem\DriverInterface;
use Magento\MediaGalleryMetadataApi\Api\AddMetadataInterface;
use Magento\MediaGalleryMetadataApi\Api\Data\MetadataInterfaceFactory;
use Magento\MediaGalleryMetadataApi\Api\ExtractMetadataInterface;
use Magento\TestFramework\Helper\Bootstrap;
use PHPUnit\Framework\TestCase;

/**
 * ExtractMetadata test
 */
class AddMetadataTest extends TestCase
{
    /**
     * @var ExtractMetadataInterface
     */
    private $addMetadata;

    /**
     * @var WriteInterface
     */
    private $varDirectory;

    /**
     * @var DriverInterface
     */
    private $driver;

    /**
     * @var MetadataInterfaceFactory
     */
    private $metadataFactory;

    /**
     * @var ExtractMetadataInterface
     */
    private $extractMetadata;

    /**
     * @inheritdoc
     */
    protected function setUp(): void
    {
        $this->addMetadata = Bootstrap::getObjectManager()->get(AddMetadataInterface::class);
        $this->varDirectory = Bootstrap::getObjectManager()->get(Filesystem::class)
            ->getDirectoryWrite(DirectoryList::VAR_DIR);
        $this->driver = Bootstrap::getObjectManager()->get(DriverInterface::class);
        $this->metadataFactory = Bootstrap::getObjectManager()->get(MetadataInterfaceFactory::class);
        $this->extractMetadata = Bootstrap::getObjectManager()->get(ExtractMetadataInterface::class);
    }

    /**
     * Test for ExtractMetadata::execute
     *
     * @dataProvider filesProvider
     * @param string $fileName
     * @param string $title
     * @param string $description
     * @param array $keywords
     * @throws LocalizedException
     */
    public function testExecute(
        string $fileName,
        string $title,
        string $description,
        array $keywords
    ): void {
        $path = realpath(__DIR__ . '/../../_files/' . $fileName);
        $modifiableFilePath = $this->varDirectory->getAbsolutePath($fileName);
        $this->driver->copy(
            $path,
            $modifiableFilePath
        );
        $metadata = $this->metadataFactory->create([
            'title' => $title,
            'description' => $description,
            'keywords' => $keywords
        ]);

        $this->addMetadata->execute($modifiableFilePath, $metadata);

        $updatedMetadata = $this->extractMetadata->execute($modifiableFilePath);

        $this->assertEquals($title, $updatedMetadata->getTitle());
        $this->assertEquals($description, $updatedMetadata->getDescription());
        $this->assertEquals($keywords, $updatedMetadata->getKeywords());

        $this->driver->deleteFile($modifiableFilePath);
    }

    /**
     * Data provider for testExecute
     *
     * @return array[]
     */
    public function filesProvider(): array
    {
        return [
            [
                'macos-photos.jpeg',
                'Updated Title',
                'Updated Description',
                [
                    'magento2',
                    'mediagallery'
                ]
            ],
            [
                'iptc_only.jpeg',
                'Updated Title',
                'Updated Description',
                [
                    'magento2',
                    'mediagallery'
                ]
            ],
            [
                'macos-preview.png',
                'Title of the magento image 2',
                'Description of the magento image 2',
                [
                    'magento2',
                    'community'
                ]
            ],
            [
                'empty_xmp_image.jpeg',
                'Title of the magento image',
                'Description of the magento image 2',
                [
                    'magento2',
                    'community'
                ]
            ]
        ];
    }
}
