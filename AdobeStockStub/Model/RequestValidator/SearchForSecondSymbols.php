<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

declare(strict_types=1);

namespace Magento\AdobeStockStub\Model\RequestValidator;

/**
 * Validate that the search on the second symbols
 */
class SearchForSecondSymbols implements RequestValidatorInterface
{
    /**
     * Validate second symbol condition in the request URL.
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
