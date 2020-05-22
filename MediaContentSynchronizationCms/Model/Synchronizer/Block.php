<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Magento\MediaContentSynchronizationCms\Model\Synchronizer;

use Magento\Framework\Reflection\DataObjectProcessor;
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
     * @var DataObjectProcessor
     */
    private $dataObjectProcessor;

    /**
     * @var array
     */
    private $fields;

    /**
     * Synchronize block content with assets
     *
     * @param ContentIdentityInterfaceFactory $contentIdentityFactory
     * @param DataObjectProcessor $dataObjectProcessor
     * @param UpdateContentAssetLinksInterface $updateContentAssetLinks
     * @param SelectByBatchesGenerator $selectBatches
     * @param array $fields
     */
    public function __construct(
        ContentIdentityInterfaceFactory $contentIdentityFactory,
        DataObjectProcessor $dataObjectProcessor,
        UpdateContentAssetLinksInterface $updateContentAssetLinks,
        SelectByBatchesGenerator $selectBatches,
        array $fields = []
    ) {
        $this->contentIdentityFactory = $contentIdentityFactory;
        $this->dataObjectProcessor = $dataObjectProcessor;
        $this->updateContentAssetLinks = $updateContentAssetLinks;
        $this->fields = $fields;
        $this->selectBatches = $selectBatches;
    }

    /**
     * Synchronize assets and contents
     */
    public function execute(): void
    {
        foreach ($this->selectBatches->execute(self::CMS_BLOCK_TABLE, [self::CMS_BLOCK_TABLE_ENTITY_ID]) as $batch) {
            foreach ($batch as $blockId) {
                foreach ($this->fields as $field) {
                    $this->updateContentAssetLinks->execute(
                        $this->contentIdentityFactory->create(
                            [
                                self::TYPE => self::CONTENT_TYPE,
                                self::FIELD => $field,
                                self::ENTITY_ID => $blockId
                            ]
                        ),
                        (string) $this->dataObjectProcessor->buildOutputDataArray(
                            $blockId,
                            BlockInterface::class
                        )
                        [$field]
                    );
                }
            }
        }
    }
}
