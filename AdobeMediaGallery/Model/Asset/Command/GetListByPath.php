<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Magento\AdobeMediaGallery\Model\Asset\Command;

use Magento\AdobeMediaGalleryApi\Api\Data\AssetInterface;
use Magento\AdobeMediaGalleryApi\Api\Data\AssetInterfaceFactory;
use Magento\AdobeMediaGalleryApi\Model\Asset\Command\GetListByPathInterface;
use Magento\Framework\App\ResourceConnection;
use Magento\Framework\Exception\IntegrationException;
use Psr\Log\LoggerInterface;

/**
 * Class GetListByIds
 */
class GetListByPath implements GetListByPathInterface
{
    private const TABLE_MEDIA_GALLERY_ASSET = 'media_gallery_asset';

    /**
     * @var ResourceConnection
     */
    private $resourceConnection;

    /**
     * @var AssetInterface
     */
    private $mediaAssetFactory;

    /**
     * @var LoggerInterface
     */
    private $logger;

    /**
     * GetListByPath constructor.
     *
     * @param ResourceConnection $resourceConnection
     * @param AssetInterfaceFactory $mediaAssetFactory
     * @param LoggerInterface $logger
     */
    public function __construct(
        ResourceConnection $resourceConnection,
        AssetInterfaceFactory $mediaAssetFactory,
        LoggerInterface $logger
    ) {
        $this->resourceConnection = $resourceConnection;
        $this->mediaAssetFactory = $mediaAssetFactory;
        $this->logger = $logger;
    }

    /**
     * Return media asset asset list
     *
     * @param string $mediaFilePath
     *
     * @return array
     * @throws IntegrationException
     */
    public function execute(string $mediaFilePath): array
    {
        try {
            $connection = $this->resourceConnection->getConnection();
            $select = $connection->select()
                ->from(self::TABLE_MEDIA_GALLERY_ASSET)
                ->where('path in (?)', $mediaFilePath);
            $data = $connection->fetchAssoc($select);

            $mediaAssets = [];
            foreach ($data as $id => $assetData) {
                $mediaAssets[$id] = $this->mediaAssetFactory->create(['data' => $assetData]);
            }

            return $mediaAssets;
        } catch (\Exception $exception) {
            $message = __('An error occurred during get media asset list: %1', $exception->getMessage());
            $this->logger->critical($message);
            throw new IntegrationException($message, $exception);
        }
    }
}
