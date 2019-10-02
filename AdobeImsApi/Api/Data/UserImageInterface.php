<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

declare(strict_types=1);

namespace Magento\AdobeImsApi\Api\Data;

/**
 * Class UserImageInterface
 * @api
 */
interface UserImageInterface
{
    /**
     * Retrieve user image url.
     *
     * @return array|null
     */
    public function getImages():? array;

}
