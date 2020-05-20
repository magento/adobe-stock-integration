<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Magento\MediaContentSynchronizationCatalog\Model\Synchronizer;

use Magento\Catalog\Api\ProductRepositoryInterface;
use Magento\Framework\Api\SearchCriteriaBuilder;
use Magento\MediaContentApi\Api\Data\ContentIdentityInterfaceFactory;
use Magento\MediaContentApi\Api\UpdateContentAssetLinksInterface;
use Magento\MediaContentApi\Model\GetEntityContentsInterface;
use Magento\MediaContentSynchronizationApi\Api\SynchronizerInterface;
use Magento\MediaContentSynchronization\Model\BatchGenerator;

/**
 * Synchronize product content with assets
 */
class Product implements SynchronizerInterface
{
    private const CONTENT_TYPE = 'catalog_product';
    private const TYPE = 'entityType';
    private const ENTITY_ID = 'entityId';
    private const FIELD = 'field';

    /**
     * @var ProductRepositoryInterface
     */
    private $repository;

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
     * @var BatchGenerator
     */
    private $batchGenerator;

    /**
     * @param ProductRepositoryInterface $repository
     * @param ContentIdentityInterfaceFactory $contentIdentityFactory
     * @param GetEntityContentsInterface $getEntityContents
     * @param UpdateContentAssetLinksInterface $updateContentAssetLinks
     * @param SearchCriteriaBuilder $searchCriteriaBuilder
     * @param BatchGenerator $batchGenerator
     * @param array $fields
     */
    public function __construct(
        ProductRepositoryInterface $repository,
        ContentIdentityInterfaceFactory $contentIdentityFactory,
        GetEntityContentsInterface $getEntityContents,
        UpdateContentAssetLinksInterface $updateContentAssetLinks,
        SearchCriteriaBuilder $searchCriteriaBuilder,
        BatchGenerator $batchGenerator,
        array $fields = []
    ) {
        $this->repository = $repository;
        $this->contentIdentityFactory = $contentIdentityFactory;
        $this->getEntityContents = $getEntityContents;
        $this->updateContentAssetLinks = $updateContentAssetLinks;
        $this->searchCriteriaBuilder = $searchCriteriaBuilder;
        $this->fields = $fields;
        $this->batchGenerator = $batchGenerator;
    }

    /**
     * @inheritdoc
     */
    public function execute(): void
    {
        $items = $this->repository->getList($this->searchCriteriaBuilder->create())->getItems();
        foreach ($this->batchGenerator->getItems($items) as $batch) {
            foreach ($batch as $item) {
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
}
