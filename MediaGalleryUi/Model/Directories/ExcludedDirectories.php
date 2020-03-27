<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Magento\MediaGalleryUi\Model\Directories;

/**
 * Excluded directories validator
 */
class ExcludedDirectories
{
    /**
     * @var array
     */
    private $excludedDirectories;

    /**
     * Constructor
     *
     * @param array $excludedDirectories
     */
    public function __construct(
        array $excludedDirectories
    ) {
        $this->excludedDirectories = $excludedDirectories;
    }

    /**
     * Validate if directory might be excluded
     *
     * @param string $path
     * @return bool
     */
    public function validate(string $path): bool
    {
        foreach ($this->excludedDirectories as $pattern) {
            preg_match($pattern, $path, $result);

            if ($result) {
                return true;
            }
        }
        return false;
    }
}
