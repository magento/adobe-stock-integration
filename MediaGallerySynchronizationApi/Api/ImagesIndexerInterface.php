<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

declare(strict_types=1);

namespace Magento\MediaGallerySynchronizationApi\Api;

/**
 * ImagesIndexer is used to read the media files across the media directory provided as a path in the method argument.
 */
interface ImagesIndexerInterface
{
    /**
     * Index image files in media gallery and execute indexers configured in DI for each file
     *
     * @return void
     */
    public function execute(): void;
}
