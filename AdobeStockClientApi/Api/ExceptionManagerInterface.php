<?php
/**
 *
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Magento\AdobeStockClientApi\Api;

/**
 * Interface ExceptionManagerInterface
 * @api
 */
interface ExceptionManagerInterface
{
    const DEFAULT_CLIENT_EXCEPTION_MESSAGE = 'Unknown error appeared during Adobe Stock client initialization.';

    const DEFAULT_CLIENT_EXCEPTION_CODE = 500;

    const FORBIDDEN_CONNECTION_ERROR_CODE = 403;


    public function processException(\Exception $exception, int $exceptionCode = self::DEFAULT_CLIENT_EXCEPTION_CODE);
}
