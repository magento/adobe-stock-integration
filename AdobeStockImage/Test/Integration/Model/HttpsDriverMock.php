<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

declare(strict_types=1);

namespace Magento\AdobeStockImage\Test\Integration\Model;

use Magento\Framework\Filesystem\Driver\Https;

/**
 * Class DriverMock represents overwritten methods
 */
class HttpsDriverMock extends Https
{
    /**
     * Retrieve file contents from given path
     *
     * @param string $path
     * @param string|null $flags
     * @param resource|null $context
     * @return string
     */
    public function fileGetContents($path, $flags = null, $context = null)
    {
        return file_get_contents($path, (bool)$flags, $context);
    }
}
