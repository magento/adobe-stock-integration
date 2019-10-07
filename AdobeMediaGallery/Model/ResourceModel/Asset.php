<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

declare(strict_types=1);

namespace  Magento\AdobeMediaGallery\Model\ResourceModel;

use Magento\AdobeMediaGalleryApi\Api\Data\AssetInterface;

/**
 * Keyword resource model
 */
class Asset
{
    /**
     * Get ID filed name
     *
     * @return string
     */
    public function getIdFieldName(): string
    {
        return AssetInterface::ID;
    }
}
