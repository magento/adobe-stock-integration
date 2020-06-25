<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

declare(strict_types=1);

namespace Magento\MediaGalleryCatalogUi\Model\Listing;

use Magento\Framework\Api\FilterBuilder;
use Magento\Framework\Api\Search\ReportingInterface;
use Magento\Framework\Api\Search\SearchCriteriaBuilder;
use Magento\Framework\Api\Search\SearchResultInterface;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\View\Element\UiComponent\DataProvider\DataProvider as UiComponentDataProvider;
use Magento\Framework\Api\Search\SearchResultFactory;
use Magento\Framework\Api\SearchCriteria\CollectionProcessorInterface;
use Magento\Catalog\Model\ResourceModel\Category\CollectionFactory;
use Magento\Framework\Api\ExtensionAttribute\JoinProcessorInterface;
use Magento\Catalog\Api\CategoryRepositoryInterface;
use Magento\Framework\Api\AttributeValueFactory;
use Magento\Framework\DataObject;

/**
 * DataProvider of customer addresses for customer address grid.
 */
class DataProvider extends UiComponentDataProvider
{

    /**
     * @var SearchResultFactory
     */
    private $searchResultFactory;

    /**
     * @var CollectionProcessorInterface
     */
    private $collectionProcessor;
    
    /**
     * @var CollectionFactory
     */
    private $categoryCollectionFactory;

    /**
     * @var CategoryRepositoryInterface
     */
    private $categoryRepository;

    /**
     * @var JoinProcessorInterface
     */
    private $extensionAttributesJoinProcessor;

    /**
     * @var AttributeValueFactory
     */
    private $attributeValueFactory;

    /**
     * DataProvider constructor.
     * @param string $name
     * @param string $primaryFieldName
     * @param string $requestFieldName
     * @param ReportingInterface $reporting
     * @param SearchCriteriaBuilder $searchCriteriaBuilder
     * @param RequestInterface $request
     * @param FilterBuilder $filterBuilder
     * @param CategoryListInterface $categoryRepository
     * @param array $meta
     * @param array $data
     * @SuppressWarnings(PHPMD.ExcessiveParameterList)
     */
    public function __construct(
        string $name,
        string $primaryFieldName,
        string $requestFieldName,
        ReportingInterface $reporting,
        SearchCriteriaBuilder $searchCriteriaBuilder,
        RequestInterface $request,
        FilterBuilder $filterBuilder,
        SearchResultFactory $searchResultFactory,
        CollectionProcessorInterface $collectionProcessor,
        CollectionFactory $categoryCollectionFactory,
        CategoryRepositoryInterface $categoryRepository,
        JoinProcessorInterface $extensionAttributesJoinProcessor,
        AttributeValueFactory $attributeValueFactory,
        array $meta = [],
        array $data = []
    ) {
        parent::__construct(
            $name,
            $primaryFieldName,
            $requestFieldName,
            $reporting,
            $searchCriteriaBuilder,
            $request,
            $filterBuilder,
            $meta,
            $data
        );
        $this->categoryRepository = $categoryRepository;
        $this->collectionProcessor = $collectionProcessor;
        $this->extensionAttributesJoinProcessor = $extensionAttributesJoinProcessor;
        $this->categoryCollectionFactory = $categoryCollectionFactory;
        $this->searchResultFactory = $searchResultFactory;
        $this->attributeValueFactory = $attributeValueFactory;
    }

    /**
     * @inheritdoc
     */
    public function getData()
    {
        try {
            return $this->searchResultToOutput($this->getSearchResult());
        } catch (\Exception $exception) {
            return [
                'items' => [],
                'totalRecords' => 0,
                'errorMessage' => $exception->getMessage()
            ];
        }
    }
    /**
     * @inheritDoc
     */
    public function getSearchResult(): SearchResultInterface
    {
        /** @var Collection $collection */
        $collection = $this->categoryCollectionFactory->create();
        $this->extensionAttributesJoinProcessor->process($collection);
        $this->collectionProcessor->process($this->getSearchCriteria(), $collection);

        $items = [];
        foreach ($collection->getData() as $categoryData) {
            $category = $this->categoryRepository->get(
                $categoryData[$collection->getEntity()->getIdFieldName()]
            );
            $items[] = $this->addAttributes(
                [
                    'name'  => $category->getName(),
                    'entity_id' => $category->getId(),
                    'image' => $category->getImage(),
                    'path' => $category->getPath(),
                    'display_mode' => $category->getDisplayMode(),
                    'products' => $category->getProductCount(),
                    'include_in_menu' => $category->getIncludeInMenu(),
                    'enabled' => $category->getIsActive()
                ]
            );
        }

        $searchResult = $this->searchResultFactory->create();
        $searchResult->setSearchCriteria($this->getSearchCriteria());
        $searchResult->setItems($items);
        $searchResult->setTotalCount($collection->getSize());
        return $searchResult;
    }

    /**
     * Add attributes to document
     *
     * @param Document $document
     * @param array $attributes [code => value]
     */
    private function addAttributes(array $attributes)
    {
        $category =  new DataObject([]);
        $customAttributes = $category->getCustomAttributes();

        foreach ($attributes as $code => $value) {
            $attribute = $this->attributeValueFactory->create();
            $attribute->setAttributeCode($code);
            $attribute->setValue($value);
            $customAttributes[$code] = $attribute;
        }

        $category->setCustomAttributes($customAttributes);

        return $category;
    }
}
