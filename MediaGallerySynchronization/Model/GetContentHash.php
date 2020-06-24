<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Magento\MediaGallerySynchronization\Model;

use Magento\MediaGallerySynchronizationApi\Model\GetContentHashInterface;
use Magento\MediaGallerySynchronization\Model\FileEncryption\Sha1File;

/**
 * Get hashed value of image content.
 */
class GetContentHash implements GetContentHashInterface
{
    /**
     * @var Sha1File
     */
    private $sha1fileHash;

    /**
     * GetContentHash constructor.
     *
     * @param Sha1File|null $sha1fileHash
     */
    public function __construct(
        Sha1File $sha1fileHash = null
    ) {
        $this->sha1fileHash = $sha1fileHash
            ?: \Magento\Framework\App\ObjectManager::getInstance()->get(Sha1File::class);
    }

    /**
     * Return the hash value of the given filepath.
     *
     * @param string $filepath
     * @return string
     */
    public function execute(string $filepath): string
    {
        return $this->sha1fileHash->hash($filepath);
    }
}
