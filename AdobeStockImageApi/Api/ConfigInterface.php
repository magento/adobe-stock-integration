<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

declare(strict_types=1);

namespace Magento\AdobeStockImageApi\Api;

/**
 * Class Config
 * @api
 */
interface ConfigInterface
{
    /**
     * Retrieve default gallery id filter value (gallery_id)
     *
     * @return string|null
     */
    public function getDefaultGalleryId(): ?string;
}
