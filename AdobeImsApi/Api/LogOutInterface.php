<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

declare(strict_types=1);

namespace Magento\AdobeImsApi\Api;

/**
 * Interface LogOutInterface
 */
interface  LogOutInterface
{
    /*
     *  LogOut User from Adobe Account
     */
    public function execute() : bool;
}
