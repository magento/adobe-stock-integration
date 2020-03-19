<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Magento\MediaContent\Model;

use Magento\Framework\Exception\IntegrationException;
use Magento\MediaContentApi\Api\ExtractAssetFromContentInterface;
use Magento\MediaGalleryApi\Api\Data\AssetInterface;
use Magento\MediaGalleryApi\Model\Asset\Command\GetByPathInterface;
use Psr\Log\LoggerInterface;

/**
 * Used for extracting media assets from a media content be the search pattern
 */
class ExtractAssetFromContent implements ExtractAssetFromContentInterface
{
    /**
     * @var string
     */
    private $searchPattern;

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
     * @param array $searchPattern
     * @param GetByPathInterface $getMediaAssetByPath
     * @param LoggerInterface $logger
     */
    public function __construct(array $searchPattern, GetByPathInterface $getMediaAssetByPath, LoggerInterface $logger)
    {
        $this->searchPattern = $searchPattern;
        $this->getMediaAssetByPath = $getMediaAssetByPath;
        $this->logger = $logger;
    }

    /**
     * @param string $content
     *
     * @return AssetInterface[]
     * @throws IntegrationException
     */
    public function execute(string $content): array
    {
        try {
            $assets = [];
            $contentDecoded = html_entity_decode($content);
            $pathMatches = [];
            foreach ($this->searchPattern as $pattern) {
                preg_match_all($pattern, $contentDecoded, $matches, PREG_PATTERN_ORDER);
                $pathMatches = array_merge($matches[1], $pathMatches);
            }

            if (isset($pathMatches[0])) {
                $assetPaths = array_unique($pathMatches);
                foreach ($assetPaths as $path) {
                    $assets[] = $this->getMediaAssetByPath->execute('/' . $path);
                }
            }

            return $assets;
        } catch (\Exception $exception) {
            $this->logger->critical($exception);
            $message = __('An error occurred while searching for asset in media content');
            throw new IntegrationException($message);
        }
    }
}
