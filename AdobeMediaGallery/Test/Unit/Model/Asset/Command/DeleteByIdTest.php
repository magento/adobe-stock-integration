<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Magento\AdobeMediaGallery\Test\Unit\Model\Asset\Command;

use Magento\AdobeMediaGallery\Model\Asset\Command\DeleteById;
use Magento\AdobeMediaGalleryApi\Api\Data\AssetInterface;
use Magento\AdobeMediaGalleryApi\Api\Data\AssetInterfaceFactory;
use Magento\AdobeMediaGalleryApi\Model\Asset\Command\GetByIdInterface;
use Magento\Framework\App\ResourceConnection;
use Magento\Framework\DB\Adapter\AdapterInterface;
use Magento\Framework\Exception\CouldNotDeleteException;
use PHPUnit\Framework\TestCase;
use Magento\Framework\TestFramework\Unit\Helper\ObjectManager;
use Psr\Log\LoggerInterface;

/**
 * Test the DeleteById command model
 */
class DeleteByIdTest extends TestCase
{
    /**
     * @var DeleteById
     */
    private $deleteMediaAssetById;

    /**
     * @var ResourceConnection
     */
    private $resourceConnection;

    /**
     * @var GetByIdInterface
     */
    private $getMediaAssetById;

    /**
     * @var AssetInterfaceFactory
     */
    private $mediaAssetFactory;

    /**
     * @var AssetInterface
     */
    private $mediaAsset;

    /**
     * @var AdapterInterface
     */
    private $adapter;

    /**
     * @var LoggerInterface
     */
    private $logger;

    /**
     * Initialize basic test class mocks
     */
    protected function setUp()
    {
        $this->resourceConnection = $this->createMock(ResourceConnection::class);
        $this->getMediaAssetById = $this->createMock(GetByIdInterface::class);
        $this->mediaAssetFactory = $this->createMock(AssetInterfaceFactory::class);
        $this->logger = $this->createMock(LoggerInterface::class);

        $this->deleteMediaAssetById = (new ObjectManager($this))->getObject(
            DeleteById::class,
            [
                'resourceConnection' => $this->resourceConnection,
                'getMediaAssetById' => $this->getMediaAssetById,
                'mediaAssetFactory' =>  $this->mediaAssetFactory,
                'logger' =>  $this->logger,
            ]
        );

        $this->mediaAsset = $this->createMock(AssetInterface::class);
        $this->adapter = $this->createMock(AdapterInterface::class);
    }

    /**
     * Test delete media asset by id action
     *
     * @param int $mediaAssetId
     * @dataProvider deleteMediaAssetTestScopes
     */
    public function testSuccessfulDeleteByIdExecution(int $mediaAssetId)
    {
        $this->getMediaAssetById->expects($this->at(0))
            ->method('execute')
            ->with($mediaAssetId)
            ->willReturn($this->mediaAsset);

        $this->resourceConnection->expects($this->at(0))
            ->method('getConnection')
            ->willReturn($this->adapter);

        $tableName = 'media_gallery_asset';
        $this->resourceConnection->expects($this->at(1))
            ->method('getTableName')
            ->with($tableName)
            ->willReturn($tableName);

        $this->mediaAsset->expects($this->once())
            ->method('getId')
            ->willReturn($mediaAssetId);

        $this->adapter->expects($this->at(0))
            ->method('delete')
            ->with($tableName, 'id = ' . $mediaAssetId);

        $this->deleteMediaAssetById->execute($mediaAssetId);
    }

    /**
     * Assume that delete action will thrown an Exception
     *
     * @param int $mediaAssetId
     * @dataProvider deleteMediaAssetTestScopes
     */
    public function testExceptionOnDeleteExecution(int $mediaAssetId)
    {
        $this->getMediaAssetById->expects($this->at(0))
            ->method('execute')
            ->with($mediaAssetId)
            ->willReturn($this->mediaAsset);

        $this->resourceConnection->expects($this->at(0))
            ->method('getConnection')
            ->willReturn($this->adapter);

        $tableName = 'media_gallery_asset';
        $this->resourceConnection->expects($this->at(1))
            ->method('getTableName')
            ->with($tableName)
            ->willReturn($tableName);

        $this->mediaAsset->expects($this->once())
            ->method('getId')
            ->willReturn($mediaAssetId);

        $testErrorText = 'test error message';
        $this->adapter->expects($this->at(0))
            ->method('delete')
            ->willThrowException(new \Exception($testErrorText));
        $this->expectException(CouldNotDeleteException::class);
        $testMessage = __(
            'Could not delete media asset with id %id: %error',
            [
                'id' => $mediaAssetId,
                'error' => $testErrorText,
            ]
        )->render();
        $this->logger->expects($this->once())
            ->method('critical')
            ->with($testMessage)
            ->willReturnSelf();
        $this->expectExceptionMessage($testMessage);
        $this->deleteMediaAssetById->execute($mediaAssetId);
    }

    /**
     * @return array
     */
    public function deleteMediaAssetTestScopes(): array
    {
        $scopes = [
            [1],
        ];

        return $scopes;
    }
}
