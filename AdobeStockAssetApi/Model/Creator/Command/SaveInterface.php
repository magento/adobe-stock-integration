<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

declare(strict_types=1);

namespace Magento\AdobeStockAssetApi\Model\Creator\Command;

use Magento\AdobeStockAssetApi\Api\Data\CreatorInterface;
use Magento\Framework\Exception\CouldNotSaveException;

/**
 * Used to save an Adobe Stock asset creator data to data storage
 */
interface SaveInterface
{
    /**
     * Save an Adobe Stock asset creator
     *
     * @param CreatorInterface $creator
     *
     * @throws CouldNotSaveException
     */
    public function execute(CreatorInterface $creator): void;
}
