<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Magento\AdobeStockClientStub\Model\Method;

/**
 * Provides the stub result for the testConnection method of the AdobeStockClient
 */
class TestConnection
{
    private const VALID_API_KEY = 'valid key';

    /**
     * Return the stub result of the test connection method
     *
     * @param string $key
     *
     * @return bool
     */
    public function execute(string $key): bool
    {
        return self::VALID_API_KEY === $key;
    }
}
