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
use Magento\MediaGallery\Model\Keyword\Command\GetAssetKeywords;
use Magento\MediaGalleryApi\Api\Data\AssetInterface;
use Magento\MediaGalleryApi\Api\Data\KeywordInterface;
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
     * @var GetAssetKeywords
     */
    private $assetKeywords;

    /**
     * @var CreateGridThumbnail
     */
    private $createGridThumbnail;

    /**
     * Constructor
     *
     * @param ResourceConnection $resource
     * @param DriverInterface $file
     * @param LoggerInterface $logger
     * @param GetAssetKeywords $assetKeywords
     * @param CreateGridThumbnail $createGridThumbnail
     */
    public function __construct(
        ResourceConnection $resource,
        DriverInterface $file,
        LoggerInterface $logger,
        GetAssetKeywords $assetKeywords,
        CreateGridThumbnail $createGridThumbnail
    ) {
        $this->resource = $resource;
        $this->file = $file;
        $this->logger = $logger;
        $this->assetKeywords = $assetKeywords;
        $this->createGridThumbnail = $createGridThumbnail;
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

            /* Get all keywords from media_gallery_keyword table for current asset */
            $keywords = array_map(function (KeywordInterface $keyword) {
                return $keyword->getKeyword();
            }, $this->assetKeywords->execute($asset->getId()));
            $thumbnailPath = $this->createGridThumbnail->execute($asset->getPath());

            $this->resource->getConnection()->insertOnDuplicate(
                $this->resource->getTableName('media_gallery_asset_grid'),
                [
                    'id' => $asset->getId(),
                    'directory' => $this->file->getParentDirectory($asset->getPath()),
                    'thumbnail_url' => $thumbnailPath,
                    'preview_url' => $asset->getPath(),
                    //phpcs:ignore Magento2.Functions.DiscouragedFunction
                    'name' => basename($asset->getPath()),
                    'content_type' => strtoupper(str_replace('image/', '', $asset->getContentType())),
                    'source' => $asset->getSource(),
                    'width' => $asset->getWidth(),
                    'height' => $asset->getHeight(),
                    'size' => $asset->getSize(),
                    'created_at' => $asset->getCreatedAt(),
                    'updated_at' => $asset->getUpdatedAt(),
                    'keywords' => implode(",", $keywords) ?: null
                ]
            );
        } catch (\Exception $exception) {
            $this->logger->critical($exception);
            $message = __('An error occurred during media asset save: %1', $exception->getMessage());
            throw new CouldNotSaveException($message, $exception);
        }
    }
}
