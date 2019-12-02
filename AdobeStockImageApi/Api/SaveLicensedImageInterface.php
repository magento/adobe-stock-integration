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
 * SaveLicensedImageInterface
 *
 * @api
 */
interface SaveLicensedImageInterface
{
    /**
     * Save licensed version of already licensed image
     *
     * @param int $mediaId
     * @param string $destinationPath
     * @throws CouldNotSaveException
     * @throws LocalizedException
     * @throws NotFoundException
     */
    public function execute(int $mediaId, string $destinationPath = null): void;
}
