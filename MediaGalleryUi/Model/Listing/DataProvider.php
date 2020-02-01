<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Magento\MediaGalleryUi\Model\Listing;

use Magento\MediaGalleryUi\Model\ResourceModel\Grid\Asset\CollectionFactory;
use Magento\Ui\DataProvider\AddFieldToCollectionInterface;
use Magento\Ui\DataProvider\AbstractDataProvider;
use Magento\Ui\DataProvider\AddFilterToCollectionInterface;

/**
 * Media gallery UI data provider. Try catch added for displaying errors in grid
 */
class DataProvider extends AbstractDataProvider
{
    /**
     * @var AddFieldToCollectionInterface[]
     */
    protected $addFieldStrategies;

    /**
     * @var AddFilterToCollectionInterface[]
     */
    protected $addFilterStrategies;

    /**
     * @param string $name
     * @param string $primaryFieldName
     * @param string $requestFieldName
     * @param CollectionFactory $collectionFactory
     * @param AddFieldToCollectionInterface[] $addFieldStrategies
     * @param AddFilterToCollectionInterface[] $addFilterStrategies
     * @param array $meta
     * @param array $data
     */
    public function __construct(
        $name,
        $primaryFieldName,
        $requestFieldName,
        CollectionFactory $collectionFactory,
        array $addFieldStrategies = [],
        array $addFilterStrategies = [],
        array $meta = [],
        array $data = []
    ) {
        parent::__construct($name, $primaryFieldName, $requestFieldName, $meta, $data);
        $this->collection = $collectionFactory->create();
        $this->addFieldStrategies = $addFieldStrategies;
        $this->addFilterStrategies = $addFilterStrategies;
    }

    /**
     * @inheritdoc
     */
    public function getData(): array
    {
        try {
            return $this->getCollection()->toArray();
        } catch (\Exception $exception) {
            return [
                'items' => [],
                'totalRecords' => 0,
                'errorMessage' => $exception->getMessage()
            ];
        }
    }

    /**
     * @inheritdoc
     */
    public function addFilter(\Magento\Framework\Api\Filter $filter): void
    {
        if (isset($this->addFilterStrategies[$filter->getField()])) {
            $this->addFilterStrategies[$filter->getField()]->addFilter(
                $this->getCollection(),
                $filter->getField(),
                [
                    $filter->getConditionType() => $filter->getValue()
                ]
            );
        } else {
            parent::addFilter($filter);
        }
    }
}
