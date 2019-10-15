<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Magento\AdobeMediaGallery\Model\Asset\Command;

use Magento\AdobeMediaGalleryApi\Api\Data\AssetInterface;
use Magento\AdobeMediaGalleryApi\Api\Data\AssetInterfaceFactory;
use Magento\AdobeMediaGalleryApi\Model\Asset\Command\GetByIdInterface;
use Magento\Framework\App\ResourceConnection;
use Magento\Framework\Exception\NoSuchEntityException;

/**
 * Class GetById
 */
class GetById implements GetByIdInterface
{
    private const TABLE_ADOBE_MEDIA_GALLERY = 'adobe_media_gallery';

    /**
     * @var ResourceConnection
     */
    private $resourceConnection;

    /**
     * @var AssetInterface
     */
    private $assetFactory;

    /**
     * GetAssetById constructor.
     *
     * @param ResourceConnection $resourceConnection
     * @param AssetInterfaceFactory $assetFactory
     */
    public function __construct(
        ResourceConnection $resourceConnection,
        AssetInterfaceFactory $assetFactory
    ) {
        $this->resourceConnection = $resourceConnection;
        $this->assetFactory = $assetFactory;
    }

    /**
     * Get media asset.
     *
     * @param int $assetId
     *
     * @return AssetInterface
     * @throws NoSuchEntityException
     */
    public function execute(int $assetId): AssetInterface
    {
        $connection = $this->resourceConnection->getConnection();
        $select = $connection->select()
            ->from(['amg' => self::TABLE_ADOBE_MEDIA_GALLERY])
            ->where('amg.id = ?', $assetId);
        $data = $connection->query($select)->fetchAll();

        if (empty($data)) {
            $message = __('There is no such media asset with "%1"', $assetId);
            throw new NoSuchEntityException($message);
        }

        /** @var AssetInterface $asset */
        $mediaAsset = $this->assetFactory->create(reset($data));

        return $mediaAsset;
    }
}
