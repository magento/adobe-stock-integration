<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Magento\MediaGalleryUi\Model;

use DateTime;
use Exception;
use Magento\Backend\Model\UrlInterface;
use Magento\Framework\App\Filesystem\DirectoryList;
use Magento\Framework\Exception\FileSystemException;
use Magento\Framework\Exception\IntegrationException;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Filesystem;
use Magento\MediaContentApi\Api\GetContentByAssetIdsInterface;
use Magento\MediaGalleryApi\Model\Asset\Command\GetByIdInterface;
use Magento\MediaGalleryApi\Model\Keyword\Command\GetAssetKeywordsInterface;
use Magento\MediaGalleryUi\Ui\Component\Listing\Columns\SourceIconProvider;
use Magento\Store\Model\Store;
use Magento\Store\Model\StoreManagerInterface;
use Psr\Log\LoggerInterface;

/**
 * Load Media Asset from database by id add all related data to it
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
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
     * @var Filesystem
     */
    private $filesystem;

    /**
     * @var SourceIconProvider
     */
    private $sourceIconProvider;

    /**
     * @var array
     */
    private $imageTypes;

    /**
     * @var GetContentByAssetIdsInterface
     */
    private $getContent;

    /**
     * @var LoggerInterface
     */
    private $logger;

    /**
     * @var array
     */
    private $assetContentTypes;

    /**
     * @var GetAssetKeywordsInterface
     */
    private $getAssetKeywords;

    /**
     * @param GetByIdInterface $getAssetById
     * @param StoreManagerInterface $storeManager
     * @param Filesystem $filesystem
     * @param SourceIconProvider $sourceIconProvider
     * @param GetAssetKeywordsInterface $getAssetKeywords
     * @param GetContentByAssetIdsInterface $getContent
     * @param LoggerInterface $logger
     * @param array $imageTypes
     * @param array $assetContentTypes
     */
    public function __construct(
        GetByIdInterface $getAssetById,
        StoreManagerInterface $storeManager,
        Filesystem $filesystem,
        SourceIconProvider $sourceIconProvider,
        GetAssetKeywordsInterface $getAssetKeywords,
        GetContentByAssetIdsInterface $getContent,
        LoggerInterface $logger,
        array $imageTypes = [],
        array $assetContentTypes = []
    ) {
        $this->getAssetById = $getAssetById;
        $this->storeManager = $storeManager;
        $this->filesystem = $filesystem;
        $this->sourceIconProvider = $sourceIconProvider;
        $this->imageTypes = $imageTypes;
        $this->getAssetKeywords = $getAssetKeywords;
        $this->getContent = $getContent;
        $this->logger = $logger;
        $this->assetContentTypes = $assetContentTypes;
    }

    /**
     * Get image details by asset ID
     *
     * @param int $assetId
     * @throws LocalizedException
     * @throws Exception
     * @return array
     */
    public function execute(int $assetId): array
    {
        $asset = $this->getAssetById->execute($assetId);

        $tags = [];
        //TODO: Must be replaced with new bulk interface: \Magento\MediaGalleryApi\Api\GetAssetsKeywordsInterface
        $keywords = $this->getAssetKeywords->execute($asset->getId());
        foreach ($keywords as $keyword) {
            $tags[] = $keyword->getKeyword();
        }

        $size = $this->getImageSize($asset->getPath());

        return [
            'image_url' => $this->getUrl($asset->getPath()),
            'title' => $asset->getTitle(),
            'path' => $asset->getPath(),
            'id' => $assetId,
            'details' => [
                [
                    'title' => __('Type'),
                    'value' => $this->getImageTypeByContentType($asset->getContentType()),
                ],
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
                ],
                [
                    'title' => __('Used In'),
                    'value' => $this->getUsedIn($assetId)
                ]
            ],
            'size' => $size,
            'tags' => $tags,
            'source' => $asset->getSource() ? $this->sourceIconProvider->getSourceIconUrl($asset->getSource()) : null,
            'content_type' => $asset->getContentType()
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
     * @return string
     * @throws Exception
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
        if ($imageSize === 0) {
            return '';
        }

        return sprintf('%sKb', $imageSize / 1000);
    }

    /**
     * Retrieve assets used in the Content
     *
     * @param int $assetId
     * @return array
     * @throws IntegrationException
     */
    private function getUsedIn(int $assetId): array
    {
        $usedIn = [];
        $contentIdentities = $this->getContent->execute([$assetId]);
        foreach ($contentIdentities as $contentIdentity) {
            $type = $this->assetContentTypes[$contentIdentity->getEntityType()] ?? $contentIdentity->getEntityType();
            if (!isset($usedIn[$type])) {
                $usedIn[$type] = 1;
            } else {
                $usedIn[$type] += 1;
            }
        }
        return $usedIn;
    }
}
