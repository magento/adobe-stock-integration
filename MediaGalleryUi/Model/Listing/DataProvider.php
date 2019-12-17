<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Magento\MediaGalleryUi\Model\Listing;

use Magento\Framework\View\Element\UiComponent\DataProvider\DataProvider as UiComponentDataProvider;

/**
 * Media gallery UI data provider
 */
class DataProvider extends UiComponentDataProvider
{
    /**
     * @inheritdoc
     */
    public function getData(): array
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
}
