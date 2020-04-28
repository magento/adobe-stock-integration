<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Magento\MediaContentSynchronization\Model;

use Magento\MediaContentSynchronizationApi\Api\SynchronizeInterface;
use Magento\MediaContentSynchronizationApi\Api\SynchronizerInterface;
use Magento\MediaContentSynchronizationApi\Model\SynchronizerPool;
use Magento\MediaContentApi\Api\Data\ContentIdentityInterfaceFactory;
use Magento\MediaContentApi\Api\Data\ContentIdentityInterface;
use Magento\MediaContentApi\Api\ExtractAssetsFromContentInterface;
use Magento\MediaGalleryApi\Api\Data\AssetInterface;
use Magento\MediaContentApi\Api\Data\ContentAssetLinkInterfaceFactory;
use Magento\MediaContentApi\Api\Data\ContentAssetLinkInterface;
use Magento\MediaContentApi\Api\SaveContentAssetLinksInterface;
use Magento\Framework\Exception\LocalizedException;
use Psr\Log\LoggerInterface;

/**
 * Synchronize content with assets
 */
class Synchronize implements SynchronizeInterface
{
    private const TYPE = 'entityType';
    private const ENTITY_ID = 'entityId';
    private const FIELD = 'field';
    private const ASSET_ID = 'assetId';
    private const CONTENT_IDENTITY = 'contentIdentity';

    /**
     * @var LoggerInterface
     */
    private $log;

    /**
     * @var ExtractAssetsFromContentInterface
     */
    private $extractAssetsFromContent;

    /**
     * @var SynchronizerPool
     */
    private $synchronizerPool;

    /**
     * @var SaveContentAssetLinksInterface
     */
    private $saveContentAssetLinks;

    /**
     * @var ContentAssetLinkInterfaceFactory
     */
    private $contentAssetLinkFactory;

    /**
     * @var ContentIdentityInterfaceFactory
     */
    private $contentIdentityFactory;

    /**
     * Synchronize constructor.
     * @param LoggerInterface $log
     * @param SynchronizerPool $synchronizerPool
     * @param ExtractAssetsFromContentInterface $extractAssetsFromContent
     * @param ContentIdentityInterfaceFactory $contentIdentityFactory
     * @param SaveContentAssetLinksInterface $saveContentAssetLinks
     * @param ContentAssetLinkInterfaceFactory $contentAssetLinkFactory
     */
    public function __construct(
        LoggerInterface $log,
        SynchronizerPool $synchronizerPool,
        ExtractAssetsFromContentInterface $extractAssetsFromContent,
        ContentIdentityInterfaceFactory $contentIdentityFactory,
        SaveContentAssetLinksInterface $saveContentAssetLinks,
        ContentAssetLinkInterfaceFactory $contentAssetLinkFactory
    ) {
        $this->log = $log;
        $this->contentIdentityFactory = $contentIdentityFactory;
        $this->synchronizerPool = $synchronizerPool;
        $this->extractAssetsFromContent = $extractAssetsFromContent;
        $this->contentAssetLinkFactory = $contentAssetLinkFactory;
        $this->saveContentAssetLinks = $saveContentAssetLinks;
    }

    /**
     * @inheritdoc
     */
    public function execute(): void
    {
        $failedItems = [];
        $contents = [];
        $links = [];

        /** @var SynchronizerInterface $synchronizer */
        foreach ($this->synchronizerPool->get() as $synchronizer) {
            try {
                $contents = array_merge($contents, $synchronizer->execute());
            } catch (\Exception $exception) {
                $this->log->critical($exception);
                $failedItems[] = $exception;
            }
        }

        if (!empty($failedItems)) {
            throw new LocalizedException(
                __('Could not synchronize content with asset')
            );
        }

        foreach ($contents as $content) {
            $links = array_merge(
                $links,
                $this->getContentAssetLink(
                    $this->extractAssetsFromContent->execute($content['content']),
                    $this->contentIdentityFactory->create(
                        [
                            self::TYPE => $content['content_type'],
                            self::FIELD => $content['field'],
                            self::ENTITY_ID => $content['entity_id']
                        ]
                    )
                )
            );
        }

        try {
            $this->saveContentAssetLinks->execute($links);
        } catch (\Exception $exception) {
            $this->log->critical($exception);
            throw new LocalizedException(
                __('Could not save content with asset links')
            );
        }
    }

    /**
     * @param AssetInterface[] $assets
     * @param ContentIdentityInterface $contentIdentity
     * @return ContentAssetLinkInterface[]
     */
    private function getContentAssetLink(array $assets, ContentIdentityInterface $contentIdentity): array
    {
        $links = [];
        foreach ($assets as $asset) {
            $links[] = $this->contentAssetLinkFactory->create([
                self::ASSET_ID => $asset->getId(),
                self::CONTENT_IDENTITY => $contentIdentity
            ]);
        }

        return $links;
    }
}
