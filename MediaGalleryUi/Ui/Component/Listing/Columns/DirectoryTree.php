<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

declare(strict_types=1);

namespace Magento\MediaGalleryUi\Ui\Component\Listing\Columns;

use Magento\Framework\Data\OptionSourceInterface;
use Magento\Framework\Filesystem;

/**
 * Content Type Photo filter options provider
 */
class DirectoryTree implements OptionSourceInterface
{
    /**
     * @var Magento\Framework\Filesystem
     */
    private $filesystem;
    /**
     * @var string
     */
    private $path;

    public function __construct(
        Filesystem $filesystem,
        string $path
    ) {
        $this->filesystem = $filesystem;
        $this->path = $path;
    }

    /**
     * Get options
     *
     * @return array
     */
    public function toOptionArray(): array
    {
        $directoryInstance = $this->filesystem->getDirectoryRead($this->path);
        $dirs = [];
        if ($directoryInstance->isDirectory()) {
            foreach ($directoryInstance->readRecursively() as $path) {
                if ($directoryInstance->isDirectory($path)) {
                    $dirs[] = [
                        'value' => $path,
                        'label' => $path
                    ];
                }
            }
        }

        return $dirs;
    }
}
