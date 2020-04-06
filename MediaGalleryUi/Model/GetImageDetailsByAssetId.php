<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Magento\MediaGalleryUi\Model;

use Magento\Backend\Model\UrlInterface;
use Magento\Framework\Exception\LocalizedException;
use Magento\MediaGalleryApi\Model\Asset\Command\GetByIdInterface;
use Magento\MediaGalleryUi\Ui\Component\Listing\Columns\SourceIconProvider;
use Magento\Store\Model\Store;
use Magento\Store\Model\StoreManagerInterface;
use DateTime;

/**
 * Load Media Asset from database by id add all related data to it
 */
class GetImageDetailsByAssetId
{
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
     * @var SourceIconProvider
     */
    private $sourceIconProvider;

    /**
     * @var array
     */
    private $imageTypes;

    /**
     * @var GetAssetGridDataById
     */
    private $getAssetGridDataById;

    /**
     * GetImageDetailsByAssetId constructor.
     *
     * @param GetByIdInterface $getAssetById
     * @param StoreManagerInterface $storeManager
     * @param SourceIconProvider $sourceIconProvider
     * @param GetAssetGridDataById $getAssetGridDataById
     * @param array $imageTypes
     */
    public function __construct(
        GetByIdInterface $getAssetById,
        StoreManagerInterface $storeManager,
        SourceIconProvider $sourceIconProvider,
        GetAssetGridDataById $getAssetGridDataById,
        array $imageTypes = []
    ) {
        $this->getAssetById = $getAssetById;
        $this->storeManager = $storeManager;
        $this->sourceIconProvider = $sourceIconProvider;
        $this->getAssetGridDataById = $getAssetGridDataById;
        $this->imageTypes = $imageTypes;
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
        $assetGridData = $this->getAssetGridDataById->execute($assetId);

        $imageUrl = $this->getUrl($asset->getPath());
        $imageType = $this->getImageTypeByContentType($assetGridData['content_type']);
        $source = $assetGridData['source']
            ? $this->sourceIconProvider->getSourceIconUrl($assetGridData['source'])
            : null;
        $tags = explode(',', $assetGridData['keywords']);
        $size = (int) $assetGridData['size'] ?? 0;
        $contentType = $assetGridData['content_type'] ?? '';

        return [
            'image_url' => $imageUrl,
            'title' => $asset->getTitle(),
            'id' => $assetId,
            'details' => [
                [
                    'title' => __('Type'),
                    'value' => $imageType,
                ],
                [
                    'title' => __('Created'),
                    'value' => $this->formatDate($assetGridData['created_at'])
                ],
                [
                    'title' => __('Modified'),
                    'value' => $this->formatDate($assetGridData['updated_at'])
                ],
                [
                    'title' => __('Width'),
                    'value' => sprintf('%spx', $assetGridData['width'])
                ],
                [
                    'title' => __('Height'),
                    'value' => sprintf('%spx', $assetGridData['height'])
                ],
                [
                    'title' => __('Size'),
                    'value' => $this->formatImageSize($size)
                ]
            ],
            'size' => $size,
            'tags' => $tags,
            'source' => $source,
            'content_type' => $contentType
        ];
    }

    /**
     * Return image type by content type
     *
     * @param string $contentType
     * @return string
     */
    private function getImageTypeByContentType(string $contentType): string
    {
        $type = current(explode('/', $contentType));

        return isset($this->imageTypes[$type]) ? $this->imageTypes[$type] : '';
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
