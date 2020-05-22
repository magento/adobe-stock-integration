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
use Magento\MediaGallerySynchronization\Model\SelectByBatchesGenerator;
use Magento\Cms\Api\Data\PageInterface;

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
     * @var DataObjectProcessor
     */
    private $dataObjectProcessor;

    /**
     * @var array
     */
    private $fields;

    /**
     * Synchronize page content with assets
     *
     * @param SelectByBatchesGenerator $selectBatches
     * @param ContentIdentityInterfaceFactory $contentIdentityFactory
     * @param UpdateContentAssetLinksInterface $updateContentAssetLinks
     * @param array $fields
     */
    public function __construct(
        SelectByBatchesGenerator $selectBatches,
        ContentIdentityInterfaceFactory $contentIdentityFactory,
        UpdateContentAssetLinksInterface $updateContentAssetLinks,
        array $fields = []
    ) {
        $this->selectBatches = $selectBatches;
        $this->contentIdentityFactory = $contentIdentityFactory;
        $this->updateContentAssetLinks = $updateContentAssetLinks;
        $this->fields = $fields;
    }

    /**
     * @inheritdoc
     */
    public function execute(): void
    {
        $columns =  array_merge([self::CMS_PAGE_TABLE_ENTITY_ID], array_values($this->fields));
        foreach ($this->selectBatches->execute(self::CMS_PAGE_TABLE, $columns) as $batch) {
            foreach ($batch as $item) {
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
    }
}
