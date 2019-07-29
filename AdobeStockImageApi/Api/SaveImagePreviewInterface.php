<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

declare(strict_types=1);

namespace Magento\AdobeStockImageApi\Api;

use Magento\Framework\Exception\CouldNotSaveException;
use Magento\Framework\Exception\NotFoundException;

/**
 * SaveImagePreviewInterface
 *
 * @api
 */
interface SaveImagePreviewInterface
{
    /**
     * Runs the save image process.
     *
     * @param int    $mediaId
     * @param string $destinationPath
     *
     * @return bool
     * @throws CouldNotSaveException
     * @throws NotFoundException
     */
    public function execute(int $mediaId, string $destinationPath): bool;
}
