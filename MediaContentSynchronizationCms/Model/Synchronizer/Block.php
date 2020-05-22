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
use Magento\MediaGallerySynchronization\Model\SelectByBatchesGenerator;

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

    /**
     * @var SelectByBatchesGenerator
     */
    private $selectBatches;

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
     * Synchronize block content with assets
     *
     * @param ContentIdentityInterfaceFactory $contentIdentityFactory
     * @param UpdateContentAssetLinksInterface $updateContentAssetLinks
     * @param SelectByBatchesGenerator $selectBatches
     * @param array $fields
     */
    public function __construct(
        ContentIdentityInterfaceFactory $contentIdentityFactory,
        UpdateContentAssetLinksInterface $updateContentAssetLinks,
        SelectByBatchesGenerator $selectBatches,
        array $fields = []
    ) {
        $this->contentIdentityFactory = $contentIdentityFactory;
        $this->updateContentAssetLinks = $updateContentAssetLinks;
        $this->fields = $fields;
        $this->selectBatches = $selectBatches;
    }

    /**
     * Synchronize assets and contents
     */
    public function execute(): void
    {
        $columns =  array_merge([self::CMS_BLOCK_TABLE_ENTITY_ID], array_values($this->fields));
        foreach ($this->selectBatches->execute(self::CMS_BLOCK_TABLE, $columns) as $batch) {
            foreach ($batch as $item) {
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
    }
}
