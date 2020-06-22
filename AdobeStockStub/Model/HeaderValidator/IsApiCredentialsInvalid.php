<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

declare(strict_types=1);

namespace Magento\AdobeStockStub\Model\HeaderValidator;

/**
 * Validate is Adobe Stock API credentials invalid.
 */
class IsApiCredentialsInvalid implements HeaderValidatorInterface
{
    /**
     * Validate is invalid API credentials condition in the request URL.
     *
     * @param array $headers
     *
     * @return bool
     */
    public function validate(array $headers): bool
    {
        return true;
    }

}
