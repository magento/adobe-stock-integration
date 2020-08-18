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
     * @var GetRenditionPathInterface
     */
    private $mediaDirectory;

    protected static $mediaDir;

    public static function setUpBeforeClass(): void
    {
        /** @var WriteInterface $mediaDirectory */
        $mediaDirectory = Bootstrap::getObjectManager()->get(
            Filesystem::class
        )->getDirectoryWrite(
            DirectoryList::MEDIA
        );

        self::$mediaDir = $mediaDirectory->getAbsolutePath();

        $fixtureDir = realpath(__DIR__ . '/../_files');

        copy($fixtureDir . self::LARGE_SIZE_IMAGE, self::$mediaDir . self::LARGE_SIZE_IMAGE);
        copy($fixtureDir . self::MEDIUM_SIZE_IMAGE, self::$mediaDir . self::MEDIUM_SIZE_IMAGE);
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

        if ($mediaDirectory->isExist(self::$mediaDir . self::LARGE_SIZE_IMAGE)) {
            $mediaDirectory->delete(self::$mediaDir . self::LARGE_SIZE_IMAGE);
        }
        if ($mediaDirectory->isExist(self::$mediaDir . self::MEDIUM_SIZE_IMAGE)) {
            $mediaDirectory->delete(self::$mediaDir . self::MEDIUM_SIZE_IMAGE);
        }
        if ($mediaDirectory->isExist(self::$mediaDir . self::RENDITIONS_FOLDER_NAME)) {
            $mediaDirectory->delete(self::$mediaDir . self::RENDITIONS_FOLDER_NAME);
        }
    }

    /**
     * Test for generation of rendition images.
     */
    public function testExecute(): void
    {
        $this->generateRenditions->execute([self::LARGE_SIZE_IMAGE, self::MEDIUM_SIZE_IMAGE]);
        $this->assertFileExists($this->mediaDirectory->getAbsolutePath(self::RENDITIONS_FOLDER_NAME . self::LARGE_SIZE_IMAGE));
        $this->assertFileDoesNotExist($this->mediaDirectory->getAbsolutePath(self::RENDITIONS_FOLDER_NAME . self::MEDIUM_SIZE_IMAGE));
    }
}
