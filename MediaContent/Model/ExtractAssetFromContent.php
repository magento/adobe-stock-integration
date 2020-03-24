<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Magento\MediaContent\Model;

use Magento\MediaContentApi\Api\ExtractAssetFromContentInterface;
use Magento\MediaGalleryApi\Api\Data\AssetInterface;
use Magento\MediaGalleryApi\Model\Asset\Command\GetByPathInterface;
use Psr\Log\LoggerInterface;

/**
 * Used for extracting media asset list from a media content by the search pattern.
 */
class ExtractAssetFromContent implements ExtractAssetFromContentInterface
{
    /**
     * @var string
     */
    private $searchPatterns;

    /**
     * @var GetByPathInterface
     */
    private $getMediaAssetByPath;

    /**
     * @var LoggerInterface
     */
    private $logger;

    /**
     * ExtractAssetFromContent constructor.
     *
     * @param array $searchPatterns
     * @param GetByPathInterface $getMediaAssetByPath
     * @param LoggerInterface $logger
     */
    public function __construct(array $searchPatterns, GetByPathInterface $getMediaAssetByPath, LoggerInterface $logger)
    {
        $this->searchPatterns = $searchPatterns;
        $this->getMediaAssetByPath = $getMediaAssetByPath;
        $this->logger = $logger;
    }

    /**
     * @inheritDoc
     */
    public function execute(string $content): array
    {
        try {
            $contentDecoded = html_entity_decode($content);
            $pathMatches = [];
            foreach ($this->searchPatterns as $pattern) {
                preg_match_all($pattern, $contentDecoded, $matches, PREG_PATTERN_ORDER);
                if (isset($matches[1]) && isset($matches[1][0])) {
                    $uniqueMatches = array_unique($matches[1]);
                    $pathMatches += $uniqueMatches;
                }
            }
            $assets = $this->loadAssetsFromContent($pathMatches);

            return $assets;
        } catch (\Exception $exception) {
            $this->logger->critical($exception);
        }
    }

    /**
     * Load media assets from the found content media paths
     *
     * @param array $pathMatches
     *
     * @return AssetInterface[]
     */
    private function loadAssetsFromContent(array $pathMatches): array
    {
        $assets = [];
        if (isset($pathMatches[0])) {
            $assetPaths = array_unique($pathMatches);
            foreach ($assetPaths as $path) {
                try {
                    /** @var AssetInterface $asset */
                    $asset = $this->getMediaAssetByPath->execute('/' . $path);
                    $assets[$asset->getId()] = $asset;
                } catch (\Exception $exception) {
                    $this->logger->critical($exception);
                }
            }
        }

        return $assets;
    }
}
