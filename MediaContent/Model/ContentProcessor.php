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
use Magento\Framework\Exception\CouldNotSaveException;
use Magento\Framework\Exception\CouldNotDeleteException;
use Psr\Log\LoggerInterface;

/**
 * Process relation managing between media asset and content: assign or unassign relation if exists.
 */
class ContentProcessor
{
    /**
     * @var ExtractAssetFromContentInterface
     */
    private $extractAssetFromContent;

    /**
     * @var AssignAsset
     */
    private $assignAsset;

    /**
     * @var GetAssetsUsedInContent
     */
    private $getAssetsUsedInContent;

    /**
     * @var UnassignAsset
     */
    private $unassignAsset;

    /**
     * @var LoggerInterface
     */
    private $logger;

    /**
     * ContentProcessor constructor.
     *
     * @param ExtractAssetFromContentInterface $extractAssetFromContent
     * @param AssignAsset $assignAsset
     * @param GetAssetsUsedInContent $getAssetsUsedInContent
     * @param UnassignAsset $unassignAsset
     * @param LoggerInterface $logger
     */
    public function __construct(
        ExtractAssetFromContentInterface $extractAssetFromContent,
        AssignAsset $assignAsset,
        GetAssetsUsedInContent $getAssetsUsedInContent,
        UnassignAsset $unassignAsset,
        LoggerInterface $logger
    ) {
        $this->extractAssetFromContent = $extractAssetFromContent;
        $this->assignAsset = $assignAsset;
        $this->getAssetsUsedInContent = $getAssetsUsedInContent;
        $this->unassignAsset = $unassignAsset;
        $this->logger = $logger;
    }

    /**
     * Create new relation between media asset and content or updated existed.
     *
     * @param string $contentEntityId
     * @param array $contentData
     * @param string $contentType
     *
     * @throws IntegrationException
     */
    public function execute(string $contentEntityId, array $contentData, string $contentType): void
    {
        try {
            foreach ($contentData as $contentField => $content) {
                $relations = $this->getAssetsUsedInContent->execute($contentType, $contentEntityId, $contentField);
                if (empty($relations)) {
                    $this->addNewRelation($content, $contentField, $contentEntityId, $contentType);
                    continue;
                }

                $this->updateRelation(
                    $relations,
                    $content,
                    $contentField,
                    $contentEntityId,
                    $contentType
                );
            }
        } catch (\Exception $exception) {
            $this->logger->critical($exception);
            $message = __('An error occurred at processing relation between media asset and content.');
            throw new IntegrationException($message);
        }
    }

    /**
     * Add new relation between media asset and content if any media asset presents in content.
     *
     * @param string $content
     * @param string $contentField
     * @param string $contentEntityId
     * @param string $contentType
     *
     * @throws CouldNotSaveException
     */
    private function addNewRelation(
        string $content,
        string $contentField,
        string $contentEntityId,
        string $contentType
    ) {
        $assetsInContent = $this->extractAssetFromContent->execute($content);
        if (count($assetsInContent) > 0) {
            /** @var AssetInterface $asset */
            foreach ($assetsInContent as $asset) {
                $this->assignAsset->execute($asset->getId(), $contentType, $contentEntityId, $contentField);
            }
        }
    }

    /**
     * Add newly added asset to content relation or remove not actual.
     *
     * @param array $relations
     * @param string|null $content
     * @param string $contentField
     * @param string $contentEntityId
     * @param string $contentType
     *
     * @throws CouldNotDeleteException
     * @throws CouldNotSaveException
     */
    private function updateRelation(
        array $relations,
        string $content = null,
        string $contentField,
        string $contentEntityId,
        string $contentType
    ) {
        $assetsInContent = ($content !== null) ? $this->extractAssetFromContent->execute($content) : [];
        foreach ($assetsInContent as $asset) {
            if (!isset($relations[$asset->getId()])) {
                $this->assignAsset->execute($asset->getId(), $contentType, $contentEntityId, $contentField);
                unset($assetsInContent[$asset->getId()]);
            }
        }

        foreach ($relations as $assetId => $data) {
            if (!isset($assetsInContent[$assetId])) {
                $this->unassignAsset->execute($assetId, $contentType, $contentEntityId, $contentField);
            }
        }
    }
}
