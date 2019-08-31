<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

declare(strict_types=1);

namespace Magento\AdobeStockImageApi\Api;

use Magento\Framework\Exception\CouldNotSaveException;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Exception\NotFoundException;

/**
 * SaveImageInterface
 *
 * @api
 */
interface SaveImageInterface
{
    /**
     * Runs the save image process.
     *
     * @param int    $adobeId
     * @param string $destinationPath
     *
     * @return void
     * @throws CouldNotSaveException
     * @throws NotFoundException
     * @throws LocalizedException
     */
    public function execute(int $adobeId, string $destinationPath): void;
}
