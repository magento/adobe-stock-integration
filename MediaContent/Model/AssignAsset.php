<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Magento\MediaContent\Model;

use Magento\MediaContentApi\Api\AssignAssetInterface;

class AssignAsset implements AssignAssetInterface
{
    /**
     * @inheritDoc
     */
    public function execute(int $assetId, string $contentType, string $contentEntityId, string $contentField): void
    {
        // TODO: Implement execute() method.
    }
}
