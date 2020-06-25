<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

declare(strict_types=1);

namespace Magento\AdobeStockStub\Model\Modifier;

/**
 * Validate request URL or request headers and if applicable modify response file.
 */
interface ModifierInterface
{
    /**
     * Modify response file.
     *
     * @param array $files
     *
     * @param string $url
     * @param array $headers
     *
     * @return array
     */
    public function modify(array $files, string $url, array $headers): array;
}
