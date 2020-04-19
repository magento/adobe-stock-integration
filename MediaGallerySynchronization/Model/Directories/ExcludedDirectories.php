<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Magento\MediaGallerySynchronization\Model\Directories;

use Magento\MediaGallerySynchronizationApi\Api\ExcludedDirectoriesInterface;

/**
 * Directory paths that should not be included in the media gallery
 */
class ExcludedDirectories implements ExcludedDirectoriesInterface
{
    /**
     * @var array
     */
    private $patterns;

    /**
     * @param array $patterns
     */
    public function __construct(
        array $patterns
    ) {
        $this->patterns = $patterns;
    }

    /**
     * @inheritDoc
     */
    public function isExcluded(string $path): bool
    {
        foreach ($this->patterns as $pattern) {
            preg_match($pattern, $path, $result);

            if ($result) {
                return true;
            }
        }
        return false;
    }
}
