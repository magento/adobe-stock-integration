<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magento\AdobeStockImage\Plugin\Product\Gallery;

use Magento\AdobeStockAssetApi\Api\AssetRepositoryInterface;
use Magento\AdobeStockAssetApi\Api\Data\AssetInterface;
use Magento\Catalog\Model\Product;
use Magento\Catalog\Model\Product\Gallery\Processor as ProcessorSubject;
use Magento\Framework\Api\FilterBuilder;
use Magento\Framework\Api\SearchCriteriaBuilder;
use Magento\Framework\Api\SearchCriteriaBuilderFactory;

/**
 * Ensures that metadata is remove from the database when image is deleted
 */
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
     *
     * @param ProcessorSubject $subject
     * @param ProcessorSubject $result
     * @param Product $product
     * @param string $file
     * @return ProcessorSubject
     */
    public function afterRemoveImage(ProcessorSubject $subject, $result, Product $product, $file)
    {
        if (!is_string($file)) {
            return $result;
        }
        $filters = [];
        $filters[] = $this->filterBuilder
            ->setField('path')
            ->setConditionType('eq')
            ->setValue($file)
            ->create();

        /** @var SearchCriteriaBuilder $criteriaBuilder */
        $criteriaBuilder = $this->searchCriteriaBuilderFactory->create();
        $criteriaBuilder->addFilters($filters);
        $criteria = $criteriaBuilder->create();

        /** @var AssetInterface $item */
        foreach ($this->assetRepository->getList($criteria)->getItems() as $asset) {
            $this->assetRepository->delete($asset);
        }

        return $result;
    }
}
