<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Magento\MediaGalleryUi\Ui\Component\Listing\Columns;

use Magento\Framework\View\Element\UiComponent\ContextInterface;
use Magento\Framework\View\Element\UiComponentFactory;
use Magento\Ui\Component\Listing\Columns\Column;

/**
 * Source icon url provider
 */
class SourceIconProvider extends Column
{
    private $constructSourceItemUrl;

    /**
     * SourceIconProvider constructor.
     *
     * @param ConstructSourceItemUrl $constructSourceItemUrl
     * @param ContextInterface $context
     * @param UiComponentFactory $uiComponentFactory
     * @param array $components
     * @param array $data
     */
    public function __construct(
        ContextInterface $context,
        UiComponentFactory $uiComponentFactory,
        ConstructSourceItemUrl $constructSourceItemUrl,
        array $components = [],
        array $data = []
    ) {
        parent::__construct($context, $uiComponentFactory, $components, $data);
        $this->constructSourceItemUrl = $constructSourceItemUrl;
    }

    /**
     * Prepare Data Source
     *
     * @param array $dataSource
     * @return array
     */
    public function prepareDataSource(array $dataSource): array
    {
        if (isset($dataSource['data']['items'])) {
            foreach ($dataSource['data']['items'] as &$item) {
                $item['source_icon_url'] =
                    (isset($item['source'])) ? $this->constructSourceItemUrl->execute($item['source']) : null;
            }
        }

        return $dataSource;
    }
}
