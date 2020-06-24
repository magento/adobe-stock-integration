<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Magento\MediaGalleryMetadata\Test\Integration\Model\Reader;

use Magento\MediaGalleryMetadata\Model\Reader\File as FileReader;
use Magento\TestFramework\Helper\Bootstrap;
use PHPUnit\Framework\TestCase;

/**
 * File reader test
 */
class FileTest extends TestCase
{
    /**
     * @var FileReader
     */
    private $reader;

    /**
     * @inheritdoc
     */
    protected function setUp(): void
    {
        $this->reader = Bootstrap::getObjectManager()->get(FileReader::class);
    }

    /**
     * Read exif, iptc and xmp data from an image file
     */
    public function testExecute(): void
    {
        $path = realpath(__DIR__ . '/../../../_files/macos-preview.jpeg');
        $file = $this->reader->execute($path);

        $this->assertEquals($path, $file->getPath());
        $this->assertNotEmpty($file->getCompressedImage());
        $this->assertCount(14, $file->getSegments());

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
}
