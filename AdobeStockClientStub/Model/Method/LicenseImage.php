<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Magento\AdobeStockClientStub\Model\Method;

/**
 * Emulate image license method of the AdobeStockClient
 */
class LicenseImage
{
    /**
     * Re-assign value to emulate void
     *
     * @param int $contentId
     */
    public function execute(int $contentId): void
    {
        $contentId = $contentId;
    }
}
