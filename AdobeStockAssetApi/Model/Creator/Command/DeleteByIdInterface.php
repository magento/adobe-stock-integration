<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

declare(strict_types=1);

namespace Magento\AdobeStockAssetApi\Model\Creator\Command;

use Magento\Framework\Exception\CouldNotDeleteException;

/**
 * Used to delete an Adobe Stock asset creator filtered by id from data storage.
 *
 * @api
 */
interface DeleteByIdInterface
{
    /**
     * Delete an Adobe Stock asset creator filtered by id
     *
     * @param int $creatorId
     *
     * @return void
     * @throws CouldNotDeleteException
     */
    public function execute(int $creatorId): void;
}
