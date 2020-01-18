<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

declare(strict_types=1);

namespace Magento\AdobeStockClientApi\Api\Client;

use Magento\Framework\Exception\IntegrationException;
use Magento\Framework\Webapi\Exception;

/**
 * Retrieve information for specific assets.
 * @api
 */
interface FilesInterface
{
    /**
     * Retrieve $columns asset fields for $ids assets
     *
     * @param array $ids
     * @param array $columns
     * @param string|null $locale
     *
     * @return array
     * @throws IntegrationException
     * @throws Exception
     */
    public function execute(array $ids, array $columns, string $locale = null): array;
}
