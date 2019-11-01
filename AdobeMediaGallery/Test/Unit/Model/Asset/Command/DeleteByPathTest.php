<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Magento\AdobeMediaGallery\Test\Unit\Model\Asset\Command;

use Magento\AdobeMediaGallery\Model\Asset\Command\DeleteByPath;
use Magento\AdobeMediaGalleryApi\Api\Data\AssetInterface;
use Magento\AdobeMediaGalleryApi\Model\Asset\Command\GetByIdInterface;
use Magento\Framework\App\ResourceConnection;
use Magento\Framework\DB\Adapter\AdapterInterface;
use Magento\Framework\Exception\CouldNotDeleteException;
use PHPUnit\Framework\TestCase;
use Magento\Framework\TestFramework\Unit\Helper\ObjectManager;
use Psr\Log\LoggerInterface;

/**
 * Test the DeleteByPath command model
 */
class DeleteByPathTest extends TestCase
{
    /**
     * @var DeleteByPath
     */
    private $deleteMediaAssetByPath;

    /**
     * @var AdapterInterface
     */
    private $adapter;

    /**
     * @var LoggerInterface
     */
    private $logger;

    /**
     * @var string
     */
    private $testFilePath;

    /**
     * @var string
     */
    private $mediaAssetTable;

    /**
     * Initialize basic test class mocks
     */
    protected function setUp()
    {
        $this->logger = $this->createMock(LoggerInterface::class);
        $resourceConnection = $this->createMock(ResourceConnection::class);

        $this->deleteMediaAssetByPath = (new ObjectManager($this))->getObject(
            DeleteByPath::class,
            [
                'resourceConnection' => $resourceConnection,
                'logger' =>  $this->logger,
            ]
        );

        $this->adapter = $this->createMock(AdapterInterface::class);
        $resourceConnection->expects($this->once())
            ->method('getConnection')
            ->willReturn($this->adapter);

        $this->mediaAssetTable = 'media_gallery_asset';
        $this->testFilePath = 'test-file-path/test.jpg';
    }

    /**
     * Test delete media asset by path command
     */
    public function testSuccessfulDeleteByIdExecution()
    {
        $tableName = 'media_gallery_asset';
        $this->adapter->expects($this->once())
            ->method('getTableName')
            ->with($this->mediaAssetTable)
            ->willReturn($this->mediaAssetTable);
        $this->adapter->expects($this->once())
            ->method('delete')
            ->with($tableName, ['path = ?' => $this->testFilePath]);

        $this->deleteMediaAssetByPath->execute($this->testFilePath);
    }

    /**
     * Assume that delete action will thrown an Exception
     */
    public function testExceptionOnDeleteExecution()
    {
        $tableName = 'media_gallery_asset';
        $this->adapter->expects($this->once())
            ->method('getTableName')
            ->with($this->mediaAssetTable)
            ->willReturn($this->mediaAssetTable);
        $this->adapter->expects($this->once())
            ->method('delete')
            ->with($tableName, ['path = ?' => $this->testFilePath])
            ->willThrowException(new \Exception());

        $this->expectException(CouldNotDeleteException::class);
        $this->logger->expects($this->once())
            ->method('critical')
            ->willReturnSelf();
        $this->deleteMediaAssetByPath->execute($this->testFilePath);
    }
}
