<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Magento\AdobeStockClientStub\Model\Method;

/**
 * Provide a stub result for the testConnection method of the AdobeStockClient
 */
class TestConnection
{
    private const INVALID_API_KEY = 'wrong-api-key';

    /**
     * Return the stub result of the test connection method
     *
     * @param string $key
     *
     * @return bool
     */
    public function execute(string $key): bool
    {
        return self::INVALID_API_KEY !== $key;
    }
}
