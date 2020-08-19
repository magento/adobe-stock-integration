<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

declare(strict_types=1);

namespace Magento\MediaGalleryRenditions\Test\Integration\Model;
use Magento\Framework\App\Filesystem\DirectoryList;
use Magento\Framework\Filesystem;
use Magento\Framework\Filesystem\Directory\WriteInterface;
use Magento\MediaGalleryRenditionsApi\Api\GenerateRenditionsInterface;
use Magento\TestFramework\Helper\Bootstrap;
use PHPUnit\Framework\TestCase;

class GenerateRenditionsTest extends TestCase
{
    private const MEDIUM_SIZE_IMAGE = '/magento_medium_image.jpg';

    private const LARGE_SIZE_IMAGE = '/magento_large_image.jpg';

    private const RENDITIONS_FOLDER_NAME = '.renditions';

    /**
     * @var GenerateRenditionsInterface
     */
    private $generateRenditions;

    /**
     * @var WriteInterface
     */
    private $mediaDirectory;

    public static function setUpBeforeClass(): void
    {
        /** @var WriteInterface $mediaDirectory */
        $mediaDirectory = Bootstrap::getObjectManager()->get(
            Filesystem::class
        )->getDirectoryWrite(
            DirectoryList::MEDIA
        );

        $mediaDir = $mediaDirectory->getAbsolutePath();

        $fixtureDir = realpath(__DIR__ . '/../_files');

        copy($fixtureDir . self::LARGE_SIZE_IMAGE, $mediaDir . self::LARGE_SIZE_IMAGE);
        copy($fixtureDir . self::MEDIUM_SIZE_IMAGE, $mediaDir . self::MEDIUM_SIZE_IMAGE);
    }

    protected function setup(): void
    {
        $this->generateRenditions = Bootstrap::getObjectManager()->get(GenerateRenditionsInterface::class);
        $this->filesystem = Bootstrap::getObjectManager()->get(Filesystem::class);
        $this->mediaDirectory = $this->filesystem->getDirectoryRead(DirectoryList::MEDIA);
    }

    public static function tearDownAfterClass(): void
    {
        /** @var WriteInterface $mediaDirectory */
        $mediaDirectory = Bootstrap::getObjectManager()->get(
            Filesystem::class
        )->getDirectoryWrite(
            DirectoryList::MEDIA
        );
        $mediaDir = $mediaDirectory->getAbsolutePath();
        if ($mediaDirectory->isExist($mediaDir . self::LARGE_SIZE_IMAGE)) {
            $mediaDirectory->delete($mediaDir . self::LARGE_SIZE_IMAGE);
        }
        if ($mediaDirectory->isExist($mediaDir . self::MEDIUM_SIZE_IMAGE)) {
            $mediaDirectory->delete($mediaDir . self::MEDIUM_SIZE_IMAGE);
        }
        if ($mediaDirectory->isExist($mediaDir . self::RENDITIONS_FOLDER_NAME)) {
            $mediaDirectory->delete($mediaDir . self::RENDITIONS_FOLDER_NAME);
        }
    }

    /**
     * @dataProvider renditionsImageProvider
     *
     * Test for generation of rendition images.
     */
    public function testExecute(string $path, string $renditionPath, bool $isRenditionsGenerated): void
    {
        $this->generateRenditions->execute([$path]);
        $expectedRenditionPath = $this->mediaDirectory->getAbsolutePath($renditionPath);
        if ($isRenditionsGenerated) {
            $this->assertFileExists($expectedRenditionPath);
        } else {
            $this->assertFileDoesNotExist($expectedRenditionPath);
        }
    }

    /**
     * @return array
     */
    public function renditionsImageProvider(): array
    {
        return [
            'rendition_image_not_generated' => [
                'path' => '/magento_medium_image.jpg',
                'renditionPath' => ".renditions/magento_medium_image.jpg",
                'isRenditionsGenerated' => false
            ],
            'rendition_image_generated' => [
                'path' => '/magento_large_image.jpg',
                'renditionPath' => ".renditions/magento_large_image.jpg",
                'isRenditionsGenerated' => true
            ]
        ];
    }
}
