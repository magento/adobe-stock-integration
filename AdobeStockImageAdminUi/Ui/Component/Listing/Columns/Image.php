<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magento\AdobeStockImageAdminUi\Ui\Component\Listing\Columns;

use Magento\Ui\Component\Listing\Columns\Column;

/**
 * Class Image
 */
class Image extends Column
{
    /**
     * Column name
     */
    const NAME = 'column.image';

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
                    $item[$fieldName . '_src'] = $item[$fieldName];
                }
            }
        }

        return $dataSource;
    }
}
