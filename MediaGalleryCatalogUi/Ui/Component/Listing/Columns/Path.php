<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magento\MediaGalleryCatalogUi\Ui\Component\Listing\Columns;

use Magento\Framework\View\Element\UiComponentFactory;
use Magento\Framework\View\Element\UiComponent\ContextInterface;
use Magento\Ui\Component\Listing\Columns\Column;
use Magento\Catalog\Api\CategoryRepositoryInterface;

/**
 * Class Path column for Category grid
 */
class Path extends Column
{

    /**
     * @var CategoryRepositoryInterface
     */
    private $categoryRepository;

    /**
     * @param ContextInterface $context
     * @param UiComponentFactory $uiComponentFactory
     * @param CategoryRepositoryInterface $categoryRepository
     * @param array $components
     * @param array $data
     */
    public function __construct(
        ContextInterface $context,
        UiComponentFactory $uiComponentFactory,
        CategoryRepositoryInterface $categoryRepository,
        array $components = [],
        array $data = []
    ) {
        parent::__construct($context, $uiComponentFactory, $components, $data);
        $this->categoryRepository =  $categoryRepository;
    }

    /**
     * Prepare Data Source
     *
     * @param array $dataSource
     * @return array
     */
    public function prepareDataSource(array $dataSource)
    {
        if (isset($dataSource['data']['items'])) {
            $fieldName = $this->getData('name');
            foreach ($dataSource['data']['items'] as & $item) {
                if (isset($item[$fieldName])) {
                    $item[$fieldName] = $this->getCategoryPathWithNames($item[$fieldName]);
                }
            }
        }

        return $dataSource;
    }

    /**
     * Replace category path ids with category names
     *
     * @param string $pathWithIds
     */
    private function getCategoryPathWithNames(string $pathWithIds): string
    {
        $categoryPathWithName = '';
        $categoryIds = explode('/', $pathWithIds);
        foreach ($categoryIds as $id) {
            if ($id == 1) {
                continue;
            }
            $categoryName = $this->categoryRepository->get($id)->getName();
            $categoryPathWithName .=  ' / ' . $categoryName;
        }
        return $categoryPathWithName;
    }
}
