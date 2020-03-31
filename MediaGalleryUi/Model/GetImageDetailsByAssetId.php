<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Magento\MediaGalleryUi\Model;

use Magento\Backend\Model\UrlInterface;
use Magento\Framework\App\Filesystem\DirectoryList;
use Magento\Framework\App\ResourceConnection;
use Magento\Framework\Exception\LocalizedException;
use Magento\MediaGalleryApi\Model\Asset\Command\GetByIdInterface;
use Magento\MediaGalleryUi\Ui\Component\Listing\Columns\SourceIconProvider;
use Magento\Store\Model\Store;
use Magento\Store\Model\StoreManagerInterface;
use Magento\Framework\Filesystem;
use DateTime;
use Magento\Framework\Exception\FileSystemException;

/**
 * Load Media Asset from database by id add all related data to it
 */
class GetImageDetailsByAssetId
{
    /**
     * Media gallery asset grid table
     */
    private const MEDIA_GALLERY_ASSET_GRID_TABLE = 'media_gallery_asset_grid';

    /**
     * Media gallery asset grid id
     */
    private const MEDIA_GALLERY_ASSET_GRID_ID = 'id';

    /**
     * Date format
     */
    private const DATE_FORMAT = 'd/m/Y, g:i A';

    /**
     * @var GetByIdInterface
     */
    private $getAssetById;

    /**
     * @var StoreManagerInterface
     */
    private $storeManager;

    /**
     * @var ResourceConnection
     */
    private $resource;

    /**
     * @var Filesystem
     */
    private $filesystem;

    /**
     * @var SourceIconProvider
     */
    private $sourceIconProvider;

    /**
     * GetImageDetailsByAssetId constructor.
     *
     * @param GetByIdInterface $getAssetById
     * @param StoreManagerInterface $storeManager
     * @param ResourceConnection $resource
     * @param Filesystem $filesystem
     * @param SourceIconProvider $sourceIconProvider
     */
    public function __construct(
        GetByIdInterface $getAssetById,
        StoreManagerInterface $storeManager,
        ResourceConnection $resource,
        Filesystem $filesystem,
        SourceIconProvider $sourceIconProvider
    ) {
        $this->getAssetById = $getAssetById;
        $this->storeManager = $storeManager;
        $this->resource = $resource;
        $this->filesystem = $filesystem;
        $this->sourceIconProvider = $sourceIconProvider;
    }

    /**
     * Get image details by asset ID
     *
     * @param int $assetId
     *
     * @return array
     *
     * @throws LocalizedException
     */
    public function execute(int $assetId): array
    {
        $asset = $this->getAssetById->execute($assetId);
        $assetGridData = $this->getAssetGridDataById($assetId);
        $tags = isset($assetGridData['keywords']) ? explode(',', $assetGridData['keywords']) : [];
        $type = $assetGridData['content_type'] ?? '';
        $size = $this->getImageSize($asset->getPath());

        return [
            'image_url' => $this->getUrl($asset->getPath()),
            'title' => $asset->getTitle(),
            'id' => $assetId,
            'details' => [
                [
                    'title' => __('Created'),
                    'value' => $this->formatDate($asset->getCreatedAt())
                ],
                [
                    'title' => __('Modified'),
                    'value' => $this->formatDate($asset->getUpdatedAt())
                ],
                [
                    'title' => __('Width'),
                    'value' => sprintf('%spx', $asset->getWidth())
                ],
                [
                    'title' => __('Height'),
                    'value' => sprintf('%spx', $asset->getHeight())
                ],
                [
                    'title' => __('Size'),
                    'value' => $this->formatImageSize($size)
                ]
            ],
            'size' => $size,
            'tags' => $tags,
            'source' => $asset->getSource() ? $this->sourceIconProvider->getSourceIconUrl($asset->getSource()) : null,
            'content_type' => $type
        ];
    }

    /**
     * Get URL for the provided media asset path
     *
     * @param string $path
     *
     * @return string
     *
     * @throws LocalizedException
     */
    private function getUrl(string $path): string
    {
        /** @var Store $store */
        $store = $this->storeManager->getStore();

        return $store->getBaseUrl(UrlInterface::URL_TYPE_MEDIA) . $path;
    }

    /**
     * Get asset grid data by ID
     *
     * @param int $assetId
     *
     * @return array
     */
    private function getAssetGridDataById(int $assetId): array
    {
        $connection = $this->resource->getConnection();
        $select = $connection->select();
        $select->from($this->resource->getTableName(self::MEDIA_GALLERY_ASSET_GRID_TABLE));
        $select->where(self::MEDIA_GALLERY_ASSET_GRID_ID . ' = ?', $assetId);
        $assetGridDataById = $connection->fetchAssoc($select);

        return $assetGridDataById[$assetId] ?? [];
    }

    /**
     * Format date
     *
     * @param string $date
     *
     * @return string
     */
    private function formatDate(string $date): string
    {
        $dateTime = new DateTime($date);

        return $dateTime->format(self::DATE_FORMAT);
    }

    /**
     * Get image size
     *
     * @param string $path
     *
     * @return int
     *
     * @throws FileSystemException
     */
    private function getImageSize(string $path): int
    {
        $mediaDirectory = $this->filesystem->getDirectoryWrite(DirectoryList::MEDIA);
        $imageStatistics = $mediaDirectory->stat($path);

        return (int) ($imageStatistics['size'] ?? 0);
    }

    /**
     * Format image size
     *
     * @param int $imageSize
     *
     * @return string
     */
    private function formatImageSize(int $imageSize): string
    {
        if (!$imageSize) {
            return '';
        }

        return sprintf('%sKb', $imageSize / 1000);
    }
}
