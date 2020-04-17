<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Magento\MediaGalleryUi\Ui\Component\Listing\Columns;

use Magento\Framework\View\Element\UiComponent\ContextInterface;
use Magento\Ui\Component\Listing\Columns;

/**
 * Add extra sort by field to column
 */
class Sorting extends Columns
{
    private const DEFAULT_SORT_DIRECTION = 'asc';

    /**
     * @var array
     */
    private $filterFieldsMap;

    /**
     * @param ContextInterface $context
     * @param array $filterFieldsMap
     * @param array $components
     * @param array $data
     */
    public function __construct(
        ContextInterface $context,
        array $filterFieldsMap = [],
        array $components = [],
        array $data = []
    ) {
        $this->filterFieldsMap = $filterFieldsMap;
        parent::__construct($context, $components, $data);
    }

    /**
     * @inheritdoc
     */
    public function prepare()
    {
        parent::prepare();

        $this->addSortDataToColumns();
    }

    /**
     * Add sort by field and sort direction to columns
     *
     * @return void
     */
    private function addSortDataToColumns(): void
    {
        foreach ($this->getChildComponents() as $column) {
            if (array_key_exists($column->getName(), $this->filterFieldsMap)) {
                $column->setData(
                    'config',
                    array_replace_recursive(
                        $column->getData('config'),
                        [
                            'sortByField' => $this->filterFieldsMap[$column->getName()],
                            'sortDirection' => $this->getSortDirection($column->getConfiguration()),
                        ]
                    )
                );
            }
        }
    }

    /**
     * Return sort direction
     *
     * @param array $config
     * @return string
     */
    private function getSortDirection(array $config): string
    {
        return array_key_exists('sorting', $config) ? $config['sorting'] : self::DEFAULT_SORT_DIRECTION;
    }
}
