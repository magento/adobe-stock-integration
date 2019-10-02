<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

declare(strict_types=1);

namespace Magento\AdobeIms\Model;

use Magento\AdobeImsApi\Api\Data\UserImageInterface;
use Magento\Framework\DataObject;

/**
 * Class TokenResponse
 */
class UserImage extends DataObject implements UserImageInterface
{
    /**
     * @inheritDoc
     */
    public function getImages(): ?array
    {
        return (array)$this->getData('images');
    }

}
