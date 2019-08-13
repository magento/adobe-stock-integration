<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magento\AdobeStockImage\Test\Unit\Model;

use Magento\Framework\TestFramework\Unit\Helper\ObjectManager;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Magento\AdobeStockImage\Model\Storage;

/**
 * Test for Storage Model
 */
class StorageTest extends TestCase
{
    /**
     * @var Storage
     */
    private $storage;

    /**
     * @var MockObject | \Magento\Framework\Filesystem
     */
    public $fileSystemMock;

    /**
     * @var MockObject | \Magento\Framework\Filesystem\DriverInterface
     */
    private $httpsDriverMock;

    /**
     * @var MockObject | \Magento\Framework\Filesystem\Io\File
     */
    private $fileSystemIoMock;

    /**
     * @var MockObject | \Psr\Log\LoggerInterface
     */
    private $logMock;

    public function setUp()
    {

        $this->fileSystemMock = $this->createMock(\Magento\Framework\Filesystem::class);
        $this->httpsDriverMock = $this->createMock(\Magento\Framework\Filesystem\Driver\Https::class);
        $this->fileSystemIoMock = $this->createMock(\Magento\Framework\Filesystem\Io\File::class);
        $this->logMock = $this->createMock(\Psr\Log\LoggerInterface::class);

        $this->storage = (new ObjectManager($this))->getObject(
            Storage::class,
            [
                'filesystem'   => $this->fileSystemMock,
                'driver'       => $this->httpsDriverMock,
                'fileSystemIo' => $this->fileSystemIoMock,
                'log'          => $this->logMock,
            ]
        );
    }

    /**
     * @dataProvider imagesCollectionProvider
     * @param array $imageData
     * @param string $expected
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function testSavePreview(array $imageData, string $expected)
    {
        /** @var Storage $storageMock */

        $this->fileSystemIoMock->expects($this->once())
            ->method('getPathInfo')
            ->with($imageData['preview_url'])
            ->willReturn(
                [
                    'dirname'   => 'https://t4.ftcdn.net/jpg/02/72/29/99',
                    'basename'  => '240_F_272299924_HjNOJkyyhzFVKRcSQ2TaArR7Ka6nTXRa.jpg',
                    'extension' => 'jpg',
                    'filename'  => '240_F_272299924_HjNOJkyyhzFVKRcSQ2TaArR7Ka6nTXRa',
                ]
            );
        $mediaDirectoryMock = $this->createMock(\Magento\Framework\Filesystem\Directory\Write::class);

        $content = 'content';
        $mediaDirectoryMock->expects($this->once())
            ->method('writeFile')
            ->withAnyParameters()
            ->willReturn($this->isType('integer'));

        $this->fileSystemMock->expects($this->once())
            ->method('getDirectoryWrite')
            ->with(\Magento\Framework\App\Filesystem\DirectoryList::MEDIA)
            ->willReturn($mediaDirectoryMock);

        $this->httpsDriverMock->expects($this->once())
            ->method('fileGetContents')
            ->willReturn($content);
        $assets = $this->createMock(\Magento\AdobeStockAsset\Model\Asset::class);
        $assets->expects($this->once())
            ->method('getData')
            ->willReturn($imageData);
        $this->assertSame($expected, $this->storage->save($assets));
    }

    /**
     * @return array
     */
    public function imagesCollectionProvider() :array
    {
        return [
            [
                'with_long_name_and_id' =>
                [
                    'preview_url' =>
                        'https://as2.ftcdn.net/jpg/01/08/33/57/500_F_108335799_yBDMncoeDvX1OkC8nBKRDx9uHrMrI077.jpg',
                    'id' => 123456789986542334,
                    'title' => 'Woman with backpack standing on the edge near big tropical river and looking far away'
                ],
                'expected' => 'Woman-with-backpack-standing-on--123456789986542334.jpg'
            ],
            [
                'with_short_name_and_id' =>
                [
                    'preview_url' =>
                        'https://as1.ftcdn.net/jpg/02/46/41/54/500_F_246415465_tZgm5OOV53DQHblTyWvO5taWUt0FJRw2.jpg',
                    'id' => 246415465,
                    'title' => 'Love'
                ],
                'expected' => 'Love-246415465.jpg'
            ],
            [
                'with_upper_case_name' =>
                [
                    'preview_url' =>
                        'https://as1.ftcdn.net/jpg/01/38/48/40/500_F_138484065_1enzXuW8NlkppNxSv4hVUrYoeF8qgoeY.jpg',
                    'id' => 138484065,
                    'title' => 'NOT AVAILABLE'
                ],
                'expected' => 'NOT-AVAILABLE-138484065.jpg'
            ],
            [
                'random' =>
                [
                    'preview_url' =>
                        'https://as1.ftcdn.net/jpg/01/38/48/40/500_F_138484065_1enzXuW8NlkppNxSv4hVUrYoeF8qgoeY.jpg',
                    'id' => 138484065,
                    'title' => 'NOT AVAILABLE7^-#$@!@#$'
                ],
                'expected' => 'NOT-AVAILABLE7^-#$@!@#$-138484065.jpg'
            ]
        ];
    }
}
