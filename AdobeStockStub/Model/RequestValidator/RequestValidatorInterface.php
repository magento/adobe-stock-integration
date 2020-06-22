<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

declare(strict_types=1);

namespace Magento\AdobeStockStub\Model\RequestValidator;

/**
 * Validate search request by specific condition required for Adobe Stock Integration tests.
 */
interface RequestValidatorInterface
{
    /**
     * Validate request URL for the specific condition of the test which covered with stub module.
     *
     * @param string $requestUrl
     *
     * @return bool
     */
    public function validate(string $requestUrl): bool;
}
