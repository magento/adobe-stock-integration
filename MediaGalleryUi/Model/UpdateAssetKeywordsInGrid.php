<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Magento\MediaGalleryUi\Model;

use Magento\Framework\App\ResourceConnection;
use Magento\Framework\Exception\CouldNotSaveException;
use Magento\MediaGalleryApi\Api\Data\KeywordInterface;
use Psr\Log\LoggerInterface;

/**
 * Reindex enhanced media gallery assets grid keywords
 */
class UpdateAssetKeywordsInGrid
{
    private const MEDIA_GALLERY_ASSET_GRID_TABLE = 'media_gallery_asset_grid';

    /**
     * @var ResourceConnection
     */
    private $resource;

    /**
     * @var LoggerInterface
     */
    private $logger;

    /**
     * UpdateAssetKeywordsInGrid constructor.
     *
     * @param ResourceConnection $resource
     * @param LoggerInterface $logger
     */
    public function __construct(ResourceConnection $resource, LoggerInterface $logger)
    {
        $this->resource = $resource;
        $this->logger = $logger;
    }

    /**
     * Update the grid table for the asset
     *
     * @param array $keywords
     * @param int $assetId
     *
     * @return void
     * @throws CouldNotSaveException
     */
    public function execute(array $keywords, int $assetId): void
    {
        if (!empty($keywords)) {
            try {
                /** @var KeywordInterface $keyword */
                foreach ($keywords as $keyword) {
                    $data[] = $keyword->getKeyword();
                }
                $concatenatedKeywords = implode(',', $data);
                $this->resource->getConnection()
                    ->update(
                        $this->resource->getTableName(self::MEDIA_GALLERY_ASSET_GRID_TABLE),
                        ['keywords' => $concatenatedKeywords],
                        ['id = ?' => $assetId]
                    );
            } catch (\Exception $exception) {
                $this->logger->critical($exception);
                $message = __(
                    'An error occurred during update media gallery grid keywords: %1',
                    $exception->getMessage()
                );
                throw new CouldNotSaveException($message, $exception);
            }
        }
    }
}
