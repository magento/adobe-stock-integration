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
use Magento\Framework\Filesystem\Directory\ReadInterface;
use Magento\MediaGalleryRenditionsApi\Api\GetRenditionPathInterface;
use Magento\TestFramework\Helper\Bootstrap;
use PHPUnit\Framework\TestCase;

class GetRenditionPathTest extends TestCase
{
    private const MEDIUM_SIZE_IMAGE = '/magento_medium_image.jpg';

    private const LARGE_SIZE_IMAGE = '/magento_large_image.jpg';

    /**
     * @var GetRenditionPathInterface
     */
    private $getRenditionPath;

    /**
     * @var ReadInterface
     */
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
        $this->getRenditionPath = Bootstrap::getObjectManager()->get(GetRenditionPathInterface::class);
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
    }

    /**
     * Test for getting a rendition path.
     */
    public function testExecute(): void
    {
        $expectedOriginalImagePath = $this->getRenditionPath->execute(self::MEDIUM_SIZE_IMAGE);
        $expectedRenditionImagePath = $this->getRenditionPath->execute(self::LARGE_SIZE_IMAGE);
        $this->assertEquals(self::MEDIUM_SIZE_IMAGE, $expectedOriginalImagePath);
        $this->assertEquals('.renditions' . self::LARGE_SIZE_IMAGE, $expectedRenditionImagePath);
    }
}
