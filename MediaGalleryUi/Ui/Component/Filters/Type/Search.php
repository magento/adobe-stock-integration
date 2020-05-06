<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

declare(strict_types=1);

namespace Magento\MediaGalleryUi\Ui\Component\Filters\Type;

use Magento\Framework\Api\Filter;
use Magento\Ui\Component\Filters\Type\AbstractFilter;

/**
 * Class Search by keyword
 */
class Search extends AbstractFilter
{
    private const KEYWORD_FILTER_NAME = 'media_gallery_keyword';

    /**
     * @inheritdoc
     */
    public function prepare()
    {
        $this->applyFilter();

        parent::prepare();
    }

    /**
     * Transfer filter to dataProvider
     *
     * @return void
     */
    private function applyFilter(): void
    {
        $value = (string) $this->getContext()->getRequestParam('search');

        if ($value !== '') {
            $this->getContext()->getDataProvider()->addFilter($this->getFilter($value));
        }
    }

    /**
     * Return prepared filter
     *
     * @param string $value
     * @return Filter
     */
    private function getFilter(string $value): Filter
    {
        return $this->filterBuilder
            ->setConditionType(self::KEYWORD_FILTER_NAME)
            ->setField($this->getName())
            ->setValue($value)
            ->create();
    }
}
