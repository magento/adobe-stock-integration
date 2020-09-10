<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

declare(strict_types=1);

namespace Magento\AdobeStockClient\Test\Integration\Model\Client;

use AdobeStock\Api\Client\AdobeStock;
use AdobeStock\Api\Models\StockFile;
use AdobeStock\Api\Response\Files as FilesResponse;
use Magento\AdobeStockClient\Model\Client\Files;
use Magento\AdobeStockClient\Model\ConnectionFactory;
use Magento\Framework\Exception\IntegrationException;
use Magento\TestFramework\Helper\Bootstrap;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

/**
 * Test client files for communication to Adobe Stock API.
 */
class FilesTest extends TestCase
{
    /**
     * @var Files
     */
    private $files;

    /**
     * @var AdobeStock|MockObject
     */
    private $connection;

    /**
     * Prepare objects.
     */
    protected function setUp(): void
    {
        $this->connection = $this->createMock(AdobeStock::class);
        $response = $this->createMock(FilesResponse::class);
        $response->expects($this->once())
            ->method('getFiles')
            ->willReturn($this->getStockFiles());
        $this->connection->expects($this->once())
            ->method('getFiles')
            ->willReturn($response);
        /** @var ConnectionFactory|MockObject $connectionFactory */
        $connectionFactory = $this->createMock(ConnectionFactory::class);
        $connectionFactory->expects($this->once())
            ->method('create')
            ->willReturn($this->connection);
        $this->files = Bootstrap::getObjectManager()->create(
            Files::class,
            [
                'connectionFactory' => $connectionFactory
            ]
        );
    }

    /**
     * Test execute method return data.
     *
     * @throws IntegrationException
     */
    public function testExecute(): void
    {
        $files = $this->files->execute(['1', '2', '3'], []);

        $this->assertIsArray($files);
        $this->assertCount(3, $files);
        $this->assertEquals(
            'https://test.url/2',
            $files[1]['comp_url']
        );
    }

    /**
     * Result files.
     *
     * @return StockFile[]
     */
    private function getStockFiles(): array
    {
        $stockFilesData = [
            [
                'id' => 1,
                'comp_url' => 'https://test.url/1',
                'thumbnail_240_url' => 'https://test.url/1',
                'width' => 110,
                'height' => 210,
                'some_bool_param' => false,
                'some_nullable_param' => null,
                'category' => [
                    'id' => 1,
                    'name' => 'Test'
                ]
            ],
            [
                'id' => 2,
                'comp_url' => 'https://test.url/2',
                'thumbnail_240_url' => 'https://test.url/2',
                'width' => 120,
                'height' => 220,
                'some_bool_params' => false,
                'some_nullable_param' => 1,
                'category' => [
                    'id' => 1,
                    'name' => 'Test'
                ]
            ],
            [
                'id' => 3,
                'comp_url' => 'https://test.url/3',
                'thumbnail_240_url' => 'https://test.url/3',
                'width' => 130,
                'height' => 230,
                'some_bool_params' => true,
                'some_nullable_param' => 2,
                'category' => [
                    'id' => 1,
                    'name' => 'Test'
                ]
            ],
        ];

        $stockFiles = [];
        foreach ($stockFilesData as $stockFileData) {
            $stockFiles[] = new StockFile($stockFileData);
        }

        return $stockFiles;
    }
}
