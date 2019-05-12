<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magento\AdobeStockImage\Model\Request;

use Magento\AdobeStockImageApi\Api\RequestBuilderInterface;

/**
 * Class Builder
 * @package Magento\AdobeStockImage\Model\Request
 */
class Builder implements RequestBuilderInterface
{
    /**
     * @{inheritdoc}
     */
    public function setSize(int $size): void
    {
        // TODO: Implement setSize() method.
    }

    /**
     * @{inheritdoc}
     */
    public function setOffset(int $offset): void
    {
        // TODO: Implement setOffset() method.
    }

    /**
     * @{inheritdoc}
     */
    public function setSort(array $sort): void
    {
        // TODO: Implement setSort() method.
    }

    /**
     * @{inheritdoc}
     */
    public function bind(string $placeholder, $value): void
    {
        // TODO: Implement bind() method.
    }

    /**
     * @{inheritdoc}
     */
    public function create()
    {
        // TODO: Implement create() method.
    }
}
