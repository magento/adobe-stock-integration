<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Magento\AdobeStockClientStub\Model\DataProvider;

use AdobeStock\Api\Models\LicenseEntitlementQuota;

/**
 * Represent the Adobe Stock client quota stub object
 */
class Quota
{
    /**
     * An Adobe stock quota object
     *
     * @return LicenseEntitlementQuota
     */
    public function getQuotaObject(): LicenseEntitlementQuota
    {
        $data = [
            'standard_credits_quota' => 10,
            'premium_credits_quota' => 10
        ];

        return new LicenseEntitlementQuota($data);
    }
}
