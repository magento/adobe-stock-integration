<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Magento\MediaGallerySynchronizationApi\Api;

use Magento\MediaGallerySynchronizationApi\Model\SynchronizerInterface;

/**
 * Synchronize assets from the provided path in the media storage to database
 */
interface SynchronizeFilesInterface extends SynchronizerInterface
{

}
