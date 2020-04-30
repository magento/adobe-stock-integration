<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Magento\MediaContentSynchronizationCatalog\Model\Synchronizer;

use Magento\Catalog\Api\CategoryListInterface;
use Magento\Framework\Api\SearchCriteriaBuilder;
use Magento\MediaContentApi\Api\Data\ContentIdentityInterfaceFactory;
use Magento\MediaContentApi\Api\UpdateContentAssetLinksInterface;
use Magento\MediaContentApi\Model\GetEntityContentsInterface;
use Magento\MediaContentSynchronizationApi\Api\SynchronizerInterface;

/**
 * Synchronize category content with assets
 */
class Category implements SynchronizerInterface
{
    private const CONTENT_TYPE = 'catalog_category';
    private const TYPE = 'entityType';
    private const ENTITY_ID = 'entityId';
    private const FIELD = 'field';

    /**
     * @var CategoryListInterface
     */
    private $categoryList;

    /**
     * @var SearchCriteriaBuilder
     */
    private $searchCriteriaBuilder;

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
     * @var array
     */
    private $fields;

    /**
     * @param CategoryListInterface $categoryList
     * @param ContentIdentityInterfaceFactory $contentIdentityFactory
     * @param GetEntityContentsInterface $getEntityContents
     * @param UpdateContentAssetLinksInterface $updateContentAssetLinks
     * @param SearchCriteriaBuilder $searchCriteriaBuilder
     * @param array $fields
     */
    public function __construct(
        CategoryListInterface $categoryList,
        ContentIdentityInterfaceFactory $contentIdentityFactory,
        GetEntityContentsInterface $getEntityContents,
        UpdateContentAssetLinksInterface $updateContentAssetLinks,
        SearchCriteriaBuilder $searchCriteriaBuilder,
        array $fields = []
    ) {
        $this->categoryList = $categoryList;
        $this->contentIdentityFactory = $contentIdentityFactory;
        $this->getEntityContents = $getEntityContents;
        $this->updateContentAssetLinks = $updateContentAssetLinks;
        $this->searchCriteriaBuilder = $searchCriteriaBuilder;
        $this->fields = $fields;
    }

    /**
     * @inheritdoc
     */
    public function execute(): void
    {
        foreach ($this->categoryList->getList($this->searchCriteriaBuilder->create())->getItems() as $item) {
            foreach ($this->fields as $field) {
                $contentIdentity = $this->contentIdentityFactory->create(
                    [
                        self::TYPE => self::CONTENT_TYPE,
                        self::FIELD => $field,
                        self::ENTITY_ID => $item->getId()
                    ]
                );
                $this->updateContentAssetLinks->execute(
                    $contentIdentity,
                    implode(PHP_EOL, $this->getEntityContents->execute($contentIdentity))
                );
            }
        }
    }
}
