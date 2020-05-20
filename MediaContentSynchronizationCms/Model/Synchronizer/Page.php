<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Magento\MediaContentSynchronizationCms\Model\Synchronizer;

use Magento\Cms\Api\Data\PageInterface;
use Magento\Cms\Api\PageRepositoryInterface;
use Magento\Framework\Api\SearchCriteriaBuilder;
use Magento\Framework\Reflection\DataObjectProcessor;
use Magento\MediaContentApi\Api\Data\ContentIdentityInterfaceFactory;
use Magento\MediaContentApi\Api\UpdateContentAssetLinksInterface;
use Magento\MediaContentSynchronizationApi\Api\SynchronizerInterface;
use Magento\MediaContentSynchronization\Model\BatchGenerator;

/**
 * Synchronize page content with assets
 */
class Page implements SynchronizerInterface
{
    private const CONTENT_TYPE = 'cms_page';
    private const TYPE = 'entityType';
    private const ENTITY_ID = 'entityId';
    private const FIELD = 'field';

    /**
     * @var PageRepositoryInterface
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
     * @var DataObjectProcessor
     */
    private $dataObjectProcessor;

    /**
     * @var array
     */
    private $fields;

    /**
     * @var BatchGenerator
     */
    private $batchGenerator;

    /**
     * Synchronize page content with assets
     *
     * @param PageRepositoryInterface $repository
     * @param ContentIdentityInterfaceFactory $contentIdentityFactory
     * @param UpdateContentAssetLinksInterface $updateContentAssetLinks
     * @param DataObjectProcessor $dataObjectProcessor
     * @param SearchCriteriaBuilder $searchCriteriaBuilder
     * @param BatchGenerator $batchGenerator
     * @param array $fields
     */
    public function __construct(
        PageRepositoryInterface $repository,
        ContentIdentityInterfaceFactory $contentIdentityFactory,
        UpdateContentAssetLinksInterface $updateContentAssetLinks,
        DataObjectProcessor $dataObjectProcessor,
        SearchCriteriaBuilder $searchCriteriaBuilder,
        BatchGenerator $batchGenerator,
        array $fields = []
    ) {
        $this->repository = $repository;
        $this->contentIdentityFactory = $contentIdentityFactory;
        $this->updateContentAssetLinks = $updateContentAssetLinks;
        $this->dataObjectProcessor = $dataObjectProcessor;
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
                    $this->updateContentAssetLinks->execute(
                    $this->contentIdentityFactory->create(
                        [
                            self::TYPE => self::CONTENT_TYPE,
                            self::FIELD => $field,
                            self::ENTITY_ID => $item->getId()
                        ]
                    ),
                    (string) $this->dataObjectProcessor->buildOutputDataArray($item, PageInterface::class)[$field]
                );
                }
            }
        }
    }
}
