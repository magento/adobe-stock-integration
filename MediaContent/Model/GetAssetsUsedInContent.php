<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Magento\MediaContent\Model;

use Magento\MediaContentApi\Api\GetAssetsUsedInContentInterface;

class GetAssetsUsedInContent implements GetAssetsUsedInContentInterface
{
    /**
     * @inheritDoc
     */
    public function execute(string $contentType, string $contentEntityId = null, string $contentField = null): array
    {
        // TODO: Implement execute() method.
    }
}
