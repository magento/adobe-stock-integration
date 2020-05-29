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
use Magento\MediaGallerySynchronizationApi\Model\FetchBatchesInterface;
use Magento\Cms\Api\Data\PageInterface;
use Magento\MediaContentSynchronizationApi\Model\IsSynchronizationRequiredInterface;

/**
 * Synchronize page content with assets
 */
class Page implements SynchronizerInterface
{
    private const CONTENT_TYPE = 'cms_page';
    private const TYPE = 'entityType';
    private const ENTITY_ID = 'entityId';
    private const FIELD = 'field';
    private const CMS_PAGE_TABLE = 'cms_page';
    private const CMS_PAGE_TABLE_ENTITY_ID = 'page_id';
    private const CMS_PAGE_TABLE_UPDATED_AT_FIELD = 'update_time';
    
    /**
     * @var IsSynchronizationRequiredInterface $isSynchronizationRequired
     */
    private $isSynchronizationRequired;
    
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
     * Synchronize page content with assets
     *
     * @param IsSynchronizationRequiredInterface $isSynchronizationRequired
     * @param FetchBatchesInterface $fetchBatches
     * @param ContentIdentityInterfaceFactory $contentIdentityFactory
     * @param UpdateContentAssetLinksInterface $updateContentAssetLinks
     * @param array $fields
     */
    public function __construct(
        IsSynchronizationRequiredInterface $isSynchronizationRequired,
        FetchBatchesInterface $fetchBatches,
        ContentIdentityInterfaceFactory $contentIdentityFactory,
        UpdateContentAssetLinksInterface $updateContentAssetLinks,
        array $fields = []
    ) {
        $this->isSynchronizationRequired = $isSynchronizationRequired;
        $this->fetchBatches = $fetchBatches;
        $this->contentIdentityFactory = $contentIdentityFactory;
        $this->updateContentAssetLinks = $updateContentAssetLinks;
        $this->fields = $fields;
    }

    /**
     * @inheritdoc
     */
    public function execute(): void
    {
        $columns =  array_merge(
            [
                self::CMS_PAGE_TABLE_ENTITY_ID,
                self::CMS_PAGE_TABLE_UPDATED_AT_FIELD
            ],
            array_values($this->fields)
        );
        foreach ($this->fetchBatches->execute(self::CMS_PAGE_TABLE, $columns) as $batch) {
            foreach ($batch as $item) {
                if (!$this->isSynchronizationRequired->execute($item[self::CMS_PAGE_TABLE_UPDATED_AT_FIELD])) {
                    continue;
                }

                $this->synchronizeField($item);
            }
        }
    }

    /**
     * Synchronize page entity fields
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
                        self::ENTITY_ID => $item[self::CMS_PAGE_TABLE_ENTITY_ID]
                    ]
                ),
                (string) $item[$field]
            );
        }
    }
}
