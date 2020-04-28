<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Magento\MediaContentSynchronizationCatalog\Model\Synchronizer;

use Magento\Catalog\Api\Data\CategoryInterface;
use Magento\MediaContentApi\Api\Data\ContentIdentityInterfaceFactory;
use Magento\MediaContentApi\Api\UpdateContentAssetLinksInterface;
use Magento\MediaContentSynchronizationApi\Api\SynchronizerInterface;
use Magento\MediaContentSynchronizationCatalog\Model\ResourceModel\GetContents;
use Magento\Framework\EntityManager\MetadataPool;

/**
 * Synchronize category content with assets
 */
class Category implements SynchronizerInterface
{
    private const TYPE = 'entityType';
    private const ENTITY_ID = 'entityId';
    private const FIELD = 'field';
    private const ENTITY = 'catalog_category';

    /**
     * @var MetadataPool
     */
    private $metadataPool;

    /**
     * @var array
     */
    private $fields;

    /**
     * @var GetContents
     */
    private $getContents;

    /**
     * @var UpdateContentAssetLinksInterface
     */
    private $updateContentAssetLinks;

    /**
     * @var ContentIdentityInterfaceFactory
     */
    private $contentIdentityFactory;

    /**
     * @param ContentIdentityInterfaceFactory $contentIdentityFactory
     * @param GetContents $getContents
     * @param MetadataPool $metadataPool
     * @param UpdateContentAssetLinksInterface $updateContentAssetLinks
     * @param array $fields
     */
    public function __construct(
        ContentIdentityInterfaceFactory $contentIdentityFactory,
        GetContents $getContents,
        MetadataPool $metadataPool,
        UpdateContentAssetLinksInterface $updateContentAssetLinks,
        array $fields = []
    ) {
        $this->contentIdentityFactory = $contentIdentityFactory;
        $this->getContents = $getContents;
        $this->metadataPool = $metadataPool;
        $this->updateContentAssetLinks = $updateContentAssetLinks;
        $this->fields = $fields;
    }

    /**
     * @inheritdoc
     */
    public function execute(): void
    {
        foreach ($this->fields as $field) {
            $contentsData = $this->getContents->execute(self::ENTITY, $field, $this->getEntityIdField());

            foreach ($contentsData as $contentData) {
                $this->updateContentAssetLinks->execute(
                    $this->contentIdentityFactory->create(
                        [
                            self::TYPE => $contentData['content_type'],
                            self::FIELD => $contentData['field'],
                            self::ENTITY_ID => $contentData['entity_id']
                        ]
                    ),
                    $contentData['content']
                );
            }
        }
    }

    /**
     * Retrieve entity id field name
     *
     * @return string
     * @throws \Exception
     */
    private function getEntityIdField(): string
    {
        return $this->metadataPool->getMetadata(CategoryInterface::class)->getLinkField();
    }
}
