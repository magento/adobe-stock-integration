<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Magento\MediaGallerySynchronization\Model\FileEncryption;

use Magento\MediaGallerySynchronizationApi\Model\FileEncryptionInterface;

class Sha1File implements FileEncryptionInterface
{
    /**
     * @param string $filepath
     * @return string
     */
    public function hash(string $filepath): string
    {
        return sha1_file($filepath);
    }
}
