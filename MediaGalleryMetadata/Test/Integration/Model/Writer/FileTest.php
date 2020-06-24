<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Magento\MediaGalleryMetadata\Test\Integration\Model\Writer;

use Magento\Framework\App\Filesystem\DirectoryList;
use Magento\Framework\Filesystem;
use Magento\Framework\Filesystem\Directory\WriteInterface;
use Magento\Framework\Filesystem\DriverInterface;
use Magento\MediaGalleryMetadata\Model\FileFactory;
use Magento\MediaGalleryMetadata\Model\Reader\File as FileReader;
use Magento\MediaGalleryMetadata\Model\Writer\File as FileWriter;
use Magento\TestFramework\Helper\Bootstrap;
use PHPUnit\Framework\TestCase;

/**
 * File reader test
 */
class FileTest extends TestCase
{
    private const NEW_FILE_NAME = 'new-image-file.jpeg';

    /**
     * @var FileReader
     */
    private $reader;

    /**
     * @var FileWriter
     */
    private $writer;

    /**
     * @var FileFactory
     */
    private $fileFactory;

    /**
     * @var WriteInterface
     */
    private $varDirectory;

    /**
     * @var DriverInterface
     */
    private $driver;

    /**
     * @inheritdoc
     */
    protected function setUp(): void
    {
        $this->reader = Bootstrap::getObjectManager()->get(FileReader::class);
        $this->writer = Bootstrap::getObjectManager()->get(FileWriter::class);
        $this->fileFactory = Bootstrap::getObjectManager()->get(FileFactory::class);
        $this->varDirectory = Bootstrap::getObjectManager()->get(Filesystem::class)
            ->getDirectoryWrite(DirectoryList::VAR_DIR);
        $this->driver = Bootstrap::getObjectManager()->get(DriverInterface::class);
    }

    /**
     * Read exif, iptc and xmp data from an image file
     */
    public function testExecute(): void
    {
        $path = $this->getNewFilePath();

        $this->assertFalse($this->varDirectory->isExist($path));

        $originalFile = $this->reader->execute($this->getOriginalFilePath());
        $this->writer->execute($this->fileFactory->create([
            'path' => $path,
            'compressedImage' => $originalFile->getCompressedImage(),
            'segments' => $originalFile->getSegments()
        ]));

        $this->assertTrue($this->varDirectory->isExist($path));

        $file = $this->reader->execute($path);

        $this->assertEquals($path, $file->getPath());
        $this->assertEquals($originalFile->getSegments(), $file->getSegments());
        $this->assertEquals($originalFile->getCompressedImage(), $file->getCompressedImage());
        $this->assertNotEmpty($file->getSegments());

        $exifSegmentFound = false;
        $xmpSegmentFound = false;
        $iptcSegmentFound = false;
        foreach ($file->getSegments() as $segment) {
            if ($segment->getName() == 'APP1') {
                if (strpos($segment->getData(), 'Exif') === 0) {
                    $exifSegmentFound = true;
                    $this->assertStringContainsString('Screenshot', $segment->getData());
                } else {
                    $xmpSegmentFound = true;
                    $this->assertStringContainsString('<x:xmpmeta', $segment->getData());
                }
            }
            if ($segment->getName() == 'APP13') {
                $iptcSegmentFound = true;
                $this->assertStringContainsString('mediagallermetadata', $segment->getData());
            }
        }

        $this->assertTrue($exifSegmentFound);
        $this->assertTrue($xmpSegmentFound);
        $this->assertTrue($iptcSegmentFound);
    }

    /**
     * @throws \Magento\Framework\Exception\FileSystemException
     */
    protected function tearDown(): void
    {
        $this->varDirectory->delete(self::NEW_FILE_NAME);
    }

    /**
     * @return string
     */
    private function getNewFilePath(): string
    {
        return $this->varDirectory->getAbsolutePath(self::NEW_FILE_NAME);
    }

    /**
     * @return string
     */
    private function getOriginalFilePath(): string
    {
        return realpath(__DIR__ . '/../../../_files/macos-preview.jpeg');
    }
}
