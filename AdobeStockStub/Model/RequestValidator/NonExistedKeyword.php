<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

declare(strict_types=1);

namespace Magento\AdobeStockStub\Model\RequestValidator;

/**
 * Validate if search is on not existed keyword.
 */
class NonExistedKeyword implements RequestValidatorInterface
{
    /**
     * Validate non existed keyword condition in the request URL.
     *
     * @param string $requestUrl
     *
     * @return bool
     */
    public function validate(string $requestUrl): bool
    {
        return true;
    }
}
