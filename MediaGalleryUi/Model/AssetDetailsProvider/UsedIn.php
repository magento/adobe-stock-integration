<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Magento\MediaGalleryUi\Model\AssetDetailsProvider;

use Magento\Framework\Exception\IntegrationException;
use Magento\MediaContentApi\Api\GetContentByAssetIdsInterface;
use Magento\MediaGalleryApi\Api\Data\AssetInterface;
use Magento\MediaGalleryUi\Model\AssetDetailsProviderInterface;

/**
 * Provide information on which content asset is used in
 */
class UsedIn implements AssetDetailsProviderInterface
{
    /**
     * @var GetContentByAssetIdsInterface
     */
    private $getContent;

    /**
     * @var array
     */
    private $contentTypes;

    /**
     * @param GetContentByAssetIdsInterface $getContent
     * @param array $contentTypes
     */
    public function __construct(
        GetContentByAssetIdsInterface $getContent,
        array $contentTypes = []
    ) {
        $this->getContent = $getContent;
        $this->contentTypes = $contentTypes;
    }

    /**
     * Provide information on which content asset is used in
     *
     * @param AssetInterface $asset
     * @return array
     * @throws IntegrationException
     */
    public function execute(AssetInterface $asset): array
    {
        return [
            'title' => __('Used In'),
            'value' => $this->getUsedIn($asset->getId())
        ];
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
            $type = $this->contentTypes[$contentIdentity->getEntityType()] ?? $contentIdentity->getEntityType();
            if (!isset($usedIn[$type])) {
                $usedIn[$type] = 1;
            } else {
                $usedIn[$type] += 1;
            }
        }
        return $usedIn;
    }
}
