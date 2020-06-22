<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

declare(strict_types=1);

namespace Magento\AdobeStockStub\Model\RequestValidator;

/**
 * Validate search on the localized image attributes.
 */
class SearchForLocalizedImageAttributes implements RequestValidatorInterface
{
    /**
     * Validate is localized is searched in the request URL.
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
