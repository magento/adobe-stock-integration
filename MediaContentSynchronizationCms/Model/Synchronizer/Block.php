<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Magento\MediaContentSynchronizationCms\Model\Synchronizer;

use Magento\MediaContentApi\Api\Data\ContentIdentityInterfaceFactory;
use Magento\MediaContentApi\Api\UpdateContentAssetLinksInterface;
use Magento\MediaContentSynchronizationApi\Api\SynchronizerInterface;
use Magento\Cms\Api\Data\BlockInterface;
use Magento\MediaGallerySynchronizationApi\Model\FetchBatchesInterface;
use Magento\MediaContentSynchronizationApi\Model\IsSynchronizationRequiredInterface;

/**
 * Synchronize block content with assets
 */
class Block implements SynchronizerInterface
{
    private const CONTENT_TYPE = 'cms_block';
    private const TYPE = 'entityType';
    private const ENTITY_ID = 'entityId';
    private const FIELD = 'field';
    private const CMS_BLOCK_TABLE = 'cms_block';
    private const CMS_BLOCK_TABLE_ENTITY_ID = 'block_id';
    private const CMS_BLOCK_TABLE_UPDATED_AT_FIELD = 'update_time';
    
    /**
     * @var FetchBatchesInterface
     */
    private $fetchBatches;

    /**
     * @var UpdateContentAssetLinksInterface
     */
    private $updateContentAssetLinks;

    /**
     * @var ContentIdentityInterfaceFactory
     */
    private $contentIdentityFactory;

    /**
     * @var array
     */
    private $fields;

    /**
     * @var IsSynchronizationRequiredInterface $isSynchronizationRequired
     */
    private $isSynchronizationRequired;

    /**
     * Synchronize block content with assets
     *
     * @param IsSynchronizationRequiredInterface $isSynchronizationRequired
     * @param ContentIdentityInterfaceFactory $contentIdentityFactory
     * @param UpdateContentAssetLinksInterface $updateContentAssetLinks
     * @param FetchBatchesInterface $fetchBatches
     * @param array $fields
     */
    public function __construct(
        IsSynchronizationRequiredInterface $isSynchronizationRequired,
        ContentIdentityInterfaceFactory $contentIdentityFactory,
        UpdateContentAssetLinksInterface $updateContentAssetLinks,
        FetchBatchesInterface $fetchBatches,
        array $fields = []
    ) {
        $this->isSynchronizationRequired = $isSynchronizationRequired;
        $this->contentIdentityFactory = $contentIdentityFactory;
        $this->updateContentAssetLinks = $updateContentAssetLinks;
        $this->fields = $fields;
        $this->fetchBatches = $fetchBatches;
    }

    /**
     * Synchronize assets and contents
     */
    public function execute(): void
    {
        $columns =  array_merge(
            [
                self::CMS_BLOCK_TABLE_ENTITY_ID,
                self::CMS_BLOCK_TABLE_UPDATED_AT_FIELD
            ],
            array_values($this->fields)
        );
        foreach ($this->fetchBatches->execute(self::CMS_BLOCK_TABLE, $columns) as $batch) {
            foreach ($batch as $item) {
                if (!$this->isSynchronizationRequired->execute($item[self::CMS_BLOCK_TABLE_UPDATED_AT_FIELD])) {
                    continue;
                }

                $this->synchronizeField($item);
            }
        }
    }
    
    /**
     * Synchronize block entity fields
     *
     * @param array $item
     */
    private function synchronizeField(array $item): void
    {
        foreach ($this->fields as $field) {
            $this->updateContentAssetLinks->execute(
                $this->contentIdentityFactory->create(
                    [
                        self::TYPE => self::CONTENT_TYPE,
                        self::FIELD => $field,
                        self::ENTITY_ID => $item[self::CMS_BLOCK_TABLE_ENTITY_ID]
                    ]
                ),
                (string) $item[$field]
            );
        }
    }
}
