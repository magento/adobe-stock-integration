<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Magento\MediaGallerySynchronization\Test\Integration\Model;

use Magento\Framework\Exception\FileSystemException;
use Magento\Framework\Filesystem\DriverInterface;
use Magento\MediaGallerySynchronizationApi\Model\GetContentHashInterface;
use Magento\TestFramework\Helper\Bootstrap;
use PHPUnit\Framework\TestCase;

/**
 * Test for GetContentHashInterface.
 */
class GetContentHashInterfaceTest extends TestCase
{
    /**
     * @var GetContentHashInterface
     */
    private $getContentHash;

    /**
     * @var DriverInterface
     */
    private $driver;

    /**
     * @inheritdoc
     */
    protected function setUp(): void
    {
        $this->getContentHash = Bootstrap::getObjectManager()->get(GetContentHashInterface::class);
        $this->driver = Bootstrap::getObjectManager()->get(DriverInterface::class);
    }

    /**
     * Test for GetContentHashInterface::execute
     *
     * @dataProvider filesProvider
     * @param string $firstFile
     * @param string $secondFile
     * @param bool $isEqual
     * @throws FileSystemException
     */
    public function testExecute(
        string $firstFile,
        string $secondFile,
        bool $isEqual
    ): void {
        $firstFileContent = $this->getImageContent($firstFile);
        $secondFileContent = $this->getImageContent($secondFile);

        if ($isEqual) {
            $this->assertEquals(
                $this->getContentHash->execute($firstFileContent),
                $this->getContentHash->execute($secondFileContent)
            );
        } else {
            $this->assertNotEquals(
                $this->getContentHash->execute($firstFileContent),
                $this->getContentHash->execute($secondFileContent)
            );
        }
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
                'magento.jpg',
                'magento_2.jpg',
                true
            ],
            [
                'magento.jpg',
                'magento_3.png',
                false
            ]
        ];
    }

    /**
     * Get image file content.
     *
     * @param string $filename
     * @return string
     * @throws FileSystemException
     */
    private function getImageContent(string $filename): string
    {
        $path = $this->getImageFilePath($filename);
        return $this->driver->fileGetContents($path);
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
}
