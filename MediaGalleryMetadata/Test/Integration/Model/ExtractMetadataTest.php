<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Magento\MediaGalleryMetadata\Test\Integration\Model;

use Magento\Framework\Exception\LocalizedException;
use Magento\MediaGalleryMetadataApi\Api\ExtractMetadataInterface;
use Magento\TestFramework\Helper\Bootstrap;
use PHPUnit\Framework\TestCase;

/**
 * Test for ExtractMetadata
 */
class ExtractMetadataTest extends TestCase
{
    /**
     * @var ExtractMetadataInterface
     */
    private $extractMetadata;

    /**
     * @inheritdoc
     */
    protected function setUp(): void
    {
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
        $metadata = $this->extractMetadata->execute($path);

        $this->assertEquals($title, $metadata->getTitle());
        $this->assertEquals($description, $metadata->getDescription());
        $this->assertEquals($keywords, $metadata->getKeywords());
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
                'Title of the magento image',
                'Description of the magento image',
                [
                    'magento',
                    'mediagallerymetadata'
                ]
            ],
            [
                'macos-preview.png',
                'Title of the magento image',
                'Description of the magento image',
                [
                    'magento',
                    'mediagallerymetadata'
                ]
            ],
            [
                'iptc_only.jpeg',
                'Title of the magento image',
                'Description of the magento image',
                [
                    'magento',
                    'mediagallerymetadata'
                ]
            ],
            [
                'exiftool.gif',
                'Title of the magento image',
                'Description of the magento image',
                [
                    'magento',
                    'mediagallerymetadata'
                ]
            ]
        ];
    }
}
