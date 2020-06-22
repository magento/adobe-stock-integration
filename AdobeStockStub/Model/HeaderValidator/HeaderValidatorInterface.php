<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

declare(strict_types=1);

namespace Magento\AdobeStockStub\Model\HeaderValidator;

/**
 * Validate search request headers by specific condition required for Adobe Stock Integration tests.
 */
interface HeaderValidatorInterface
{
    /**
     * Validate request headers for the specific condition of the test which covered with stub module.
     *
     * @param array $headers
     *
     * @return bool
     */
    public function validate(array $headers): bool;
}
