<?php

/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magento\AdobeStockImage\Plugin\Product\Gallery;

use Magento\AdobeStockImage\Api\AssetRepositoryInterface;
use Magento\Catalog\Model\Product;
use Magento\Catalog\Model\Product\Gallery\Processor as ProcessorSubject;
use Magento\Framework\Api\FilterBuilder;
use Magento\Framework\Api\SearchCriteriaBuilderFactory;

class Processor
{
    /**
     * @var AssetRepositoryInterface
     */
    protected $assetRepository;

    /**
     * @var FilterBuilder
     */
    protected $filterBuilder;

    /**
     * @var SearchCriteriaBuilderFactory
     */
    protected $searchCriteriaBuilderFactory;

    /**
     * Processor constructor.
     * @param AssetRepositoryInterface $assetRepository
     * @param FilterBuilder $filterBuilder
     * @param SearchCriteriaBuilderFactory $searchCriteriaBuilderFactory
     */
    public function __construct(
        AssetRepositoryInterface $assetRepository,
        FilterBuilder $filterBuilder,
        SearchCriteriaBuilderFactory $searchCriteriaBuilderFactory
    ) {
        $this->assetRepository = $assetRepository;
        $this->filterBuilder = $filterBuilder;
        $this->searchCriteriaBuilderFactory = $searchCriteriaBuilderFactory;
    }

    /**
     * Delete Adobe's stock asset after image was deleted
     * @param ProcessorSubject $subject
     * @param $result
     * @param Product $product
     * @param $file
     */
    public function afterRemoveImage(ProcessorSubject $subject, $result, Product $product, $file)
    {
        if (is_string($file)) {
            $filters = [];
            $filters[] = $this->filterBuilder
                ->setField('path')
                ->setConditionType('eq')
                ->setValue($file)
                ->create();
            $criteriaAux = $this->searchCriteriaBuilderFactory->create();
            $criteriaAux->addFilters($filters);
            $criteria = $criteriaAux->create();
            $search = $this->getList($criteria);
            $items = $search->getItems();
            if (count($items) > 0) {
                foreach ($items as $item) {
                    $id = (int)$item["id"];
                    $this->assetRepository->deleteById($id);
                }
            }
        }
    }
}
