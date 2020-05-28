<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Magento\MediaContentSynchronizationCatalog\Model\Synchronizer;

use Magento\MediaContentApi\Api\Data\ContentIdentityInterfaceFactory;
use Magento\MediaContentApi\Api\UpdateContentAssetLinksInterface;
use Magento\MediaContentApi\Model\GetEntityContentsInterface;
use Magento\MediaContentSynchronizationApi\Api\SynchronizerInterface;
use Magento\MediaGallerySynchronizationApi\Model\FetchBatchesInterface;

/**
 * Synchronize category content with assets
 */
class Category implements SynchronizerInterface
{
    private const CONTENT_TYPE = 'catalog_category';
    private const TYPE = 'entityType';
    private const ENTITY_ID = 'entityId';
    private const FIELD = 'field';
    private const CATEGORY_TABLE = 'catalog_category_entity';
    private const CATEGORY_IDENTITY_FIELD = 'entity_id';

    /**
     * @var UpdateContentAssetLinksInterface
     */
    private $updateContentAssetLinks;

    /**
     * @var ContentIdentityInterfaceFactory
     */
    private $contentIdentityFactory;

    /**
     * @var GetEntityContentsInterface
     */
    private $getEntityContents;

    /**
     * @var FetchBatchesInterface
     */
    private $selectBatches;

    /**
     * @var array
     */
    private $fields;

    /**
     * @param ContentIdentityInterfaceFactory $contentIdentityFactory
     * @param GetEntityContentsInterface $getEntityContents
     * @param UpdateContentAssetLinksInterface $updateContentAssetLinks
     * @param FetchBatchesInterface $selectBatches
     * @param array $fields
     */
    public function __construct(
        ContentIdentityInterfaceFactory $contentIdentityFactory,
        GetEntityContentsInterface $getEntityContents,
        UpdateContentAssetLinksInterface $updateContentAssetLinks,
        FetchBatchesInterface $selectBatches,
        array $fields = []
    ) {
        $this->contentIdentityFactory = $contentIdentityFactory;
        $this->getEntityContents = $getEntityContents;
        $this->updateContentAssetLinks = $updateContentAssetLinks;
        $this->fields = $fields;
        $this->selectBatches = $selectBatches;
    }

    /**
     * @inheritdoc
     */
    public function execute(): void
    {
        foreach ($this->selectBatches->execute(self::CATEGORY_TABLE, [self::CATEGORY_IDENTITY_FIELD]) as $batch) {
            foreach ($batch as $item) {
                $this->synchronizeField($item);
            }
        }
    }

    /**
     * Synchronize product entity fields
     *
     * @param array $item
     */
    private function synchronizeField(array $item): void
    {
        foreach ($this->fields as $field) {
            $contentIdentity = $this->contentIdentityFactory->create(
                [
                    self::TYPE => self::CONTENT_TYPE,
                    self::FIELD => $field,
                    self::ENTITY_ID => $item[self::CATEGORY_IDENTITY_FIELD]
                ]
            );
            $this->updateContentAssetLinks->execute(
                $contentIdentity,
                implode(PHP_EOL, $this->getEntityContents->execute($contentIdentity))
            );
        }
    }
}
