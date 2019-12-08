<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

declare(strict_types=1);

namespace Magento\AdobeStockAssetApi\Model\Category\Command;

use Magento\AdobeStockAssetApi\Api\Data\CategoryInterface;

/**
 * Used to load an Adobe Stock asset category filtered by id
 */
interface LoadByIdInterface
{
    /**
     * Load an Adobe Stock asset category
     *
     * @param int $categoryId
     *
     * @return CategoryInterface
     */
    public function execute(int $categoryId): CategoryInterface;
}
