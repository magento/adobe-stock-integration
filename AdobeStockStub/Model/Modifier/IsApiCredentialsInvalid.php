<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

declare(strict_types=1);

namespace Magento\AdobeStockStub\Model\Modifier;

/**
 * Validate is Adobe Stock API credentials invalid and modify is yes.
 */
class IsApiCredentialsInvalid implements ModifierInterface
{
    private const INCORRECT_API_KEY_USED_FOR_TESTS = 'blahblahblah';
    private const INCORRECT_API_KEY_USED_FOR_TESTS_THROWING_EXCEPTION = 'wrong-api-key';

    /**
     * Validate is invalid API credentials condition in the request URL.
     *
     * @see [Story #6] User configures Adobe Stock integration
     *
     * @param array $files
     * @param array $url
     * @param array $headers
     *
     * @return array
     * @throws \Exception
     */
    public function modify(array $files, array $url, array $headers): array
    {
        if ($headers['headers']['x-api-key'] === self::INCORRECT_API_KEY_USED_FOR_TESTS
            || $headers['headers']['x-api-key'] === self::INCORRECT_API_KEY_USED_FOR_TESTS_THROWING_EXCEPTION
        ) {
            throw new \Exception();
        }

        return $files;
    }
}
