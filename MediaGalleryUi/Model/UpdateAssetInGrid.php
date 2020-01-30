<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Magento\MediaGalleryUi\Model;

use Magento\Framework\App\ResourceConnection;
use Magento\Framework\Exception\CouldNotSaveException;
use Magento\Framework\Filesystem\DriverInterface;
use Magento\MediaGalleryApi\Api\Data\AssetInterface;
use Psr\Log\LoggerInterface;

/**
 * Reindex Media Gallery Assets Grid
 */
class UpdateAssetInGrid
{
    /**
     * @var ResourceConnection
     */
    private $resource;

    /**
     * @var DriverInterface
     */
    private $file;

    /**
     * @var LoggerInterface
     */
    private $logger;

    /**
     * Constructor
     *
     * @param ResourceConnection $resource
     * @param DriverInterface $file
     * @param LoggerInterface $logger
     */
    public function __construct(
        ResourceConnection $resource,
        DriverInterface $file,
        LoggerInterface $logger
    ) {
        $this->resource = $resource;
        $this->file = $file;
        $this->logger = $logger;
    }

    /**
     * Update the media asset grid with additional data
     *
     * @param AssetInterface $asset
     *
     * @return void
     * @throws CouldNotSaveException
     */
    public function execute(AssetInterface $asset): void
    {
        try {
            $this->resource->getConnection()->insertOnDuplicate(
                $this->resource->getTableName('media_gallery_asset_grid'),
                [
                    'id' => $asset->getId(),
                    'directory' => $this->file->getParentDirectory($asset->getPath()),
                    'thumbnail_url' => $asset->getPath(),
                    'preview_url' => $asset->getPath(),
                    'name' => basename($asset->getPath()),
                    'content_type' => strtoupper(str_replace('image/', '', $asset->getContentType())),
                    'source' => $asset->getSource(),
                    'width' => $asset->getWidth(),
                    'height' => $asset->getHeight(),
                    'created_at' => $asset->getCreatedAt(),
                    'updated_at' => $asset->getUpdatedAt(),
                ]
            );
        } catch (\Exception $exception) {
            $this->logger->critical($exception);
            $message = __('An error occurred during media asset save: %1', $exception->getMessage());
            throw new CouldNotSaveException($message, $exception);
        }
    }

    private function getIconUrl(AssetInterface $asset)
    {
        $iconUrl = $this->assetRepository->getUrlWithParams(
                'Magento_MediaGalleryUi::images/Astock.png',
                ['_secure' => $this->getIsSecure()]
            );
    }
}
