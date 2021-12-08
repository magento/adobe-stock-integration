<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

declare(strict_types=1);

namespace Magento\AdobeStockAssetApi\Model\Creator\Command;

use Magento\AdobeStockAssetApi\Api\Data\CreatorInterface;
use Magento\Framework\Exception\NoSuchEntityException;

/**
 * Used to load an Adobe Stock asset creator filtered by id
 *
 * @api
 */
interface LoadByIdInterface
{
    /**
     * Load an Adobe Stock asset creator
     *
     * @param int $creatorId
     *
     * @return CreatorInterface
     * @throws NoSuchEntityException
     */
    public function execute(int $creatorId): CreatorInterface;
}
