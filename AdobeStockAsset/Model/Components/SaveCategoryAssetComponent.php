<?php

namespace Magento\AdobeStockAsset\Model\Components;

use Magento\AdobeStockAssetApi\Api\CategoryRepositoryInterface;
use Magento\AdobeStockAssetApi\Api\Data\CategoryInterface;
use Magento\AdobeStockAssetApi\Api\Data\CategorySearchResultsInterface;
use Magento\Framework\Api\SearchCriteriaBuilder;
use Magento\Framework\Exception\AlreadyExistsException;

/**
 * Class SaveCategoryAssetComponent
 */
class SaveCategoryAssetComponent
{
    /**
     * @var CategoryRepositoryInterface
     */
    private $categoryRepository;

    /**
     * @var SearchCriteriaBuilder
     */
    private $searchCriteriaBuilder;

    /**
     * SaveCategoryAssetComponent constructor.
     *
     * @param CategoryRepositoryInterface $categoryRepository
     * @param SearchCriteriaBuilder       $searchCriteriaBuilder
     */
    public function __construct(
        CategoryRepositoryInterface $categoryRepository,
        SearchCriteriaBuilder $searchCriteriaBuilder
    ) {
        $this->categoryRepository = $categoryRepository;
        $this->searchCriteriaBuilder = $searchCriteriaBuilder;
    }

    /**
     * Create a new adobe stock asset category if not exists.
     *
     * @param CategoryInterface $category
     *
     * @return CategoryInterface
     */
    public function execute(CategoryInterface $category): CategoryInterface
    {
        try {
            $searchCriteria = $this->searchCriteriaBuilder
                ->addFilter(CategoryInterface::ADOBE_ID, $category->getAdobeId())
                ->create();

            /** @var CategorySearchResultsInterface $categoryList */
            $categoryList = $this->categoryRepository->getList($searchCriteria);
            if (0 === $categoryList->getTotalCount()) {
                $category = $this->categoryRepository->save($category);
            } else {
                $categoryItems = $categoryList->getItems();
                $category = reset($categoryItems);
            }
            return $category;
        } catch (AlreadyExistsException $exception) {
            return $category;
        }
    }
}
