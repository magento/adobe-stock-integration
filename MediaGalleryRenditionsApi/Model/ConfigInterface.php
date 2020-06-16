<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

declare(strict_types=1);

namespace Magento\MediaGalleryRenditionsApi\Model;

/**
 * Class responsible for providing API access to Media Gallery Renditions system configuration
 */
interface ConfigInterface
{
    /**
     * Get width value specified in Media Gallery Renditions
     *
     * @return int
     */
    public function getWidth(): int;

    /**
     * Get length value specified in Media Gallery Renditions
     *
     * @return int
     */
    public function getHeight(): int;
}
