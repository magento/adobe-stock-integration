<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

declare(strict_types=1);

namespace Magento\AdobeStockAssetApi\Model\Category\Command;

use Magento\AdobeStockAssetApi\Api\Data\CategoryInterface;
use Magento\Framework\Exception\NoSuchEntityException;

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
     * @throws NoSuchEntityException
     */
    public function execute(int $categoryId): CategoryInterface;
}
