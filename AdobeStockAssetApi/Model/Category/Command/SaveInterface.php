<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

declare(strict_types=1);

namespace Magento\AdobeStockAssetApi\Model\Category\Command;

use Magento\AdobeStockAssetApi\Api\Data\CategoryInterface;
use Magento\Framework\Exception\CouldNotSaveException;

/**
 * Used to save an Adobe Stock asset category data to data storage
 *
 * @api
 */
interface SaveInterface
{
    /**
     * Save an Adobe Stock asset category
     *
     * @param CategoryInterface $category
     *
     * @throws CouldNotSaveException
     */
    public function execute(CategoryInterface $category): void;
}
