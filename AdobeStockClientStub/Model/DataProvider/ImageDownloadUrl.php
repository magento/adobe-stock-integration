<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Magento\AdobeStockClientStub\Model\DataProvider;

use AdobeStock\Api\Response\License as LicenseResponse;

/**
 * Provide a stub image URL
 */
class ImageDownloadUrl
{
    private const IMAGE_DOWNLOAD_URL = 'http://image_url.jpg';

    private const EMPTY_IMAGE_DOWNLOAD_URL = '';

    /**
     * Return an image download URL
     *
     * @param int $contentId
     *
     * @return string
     */
    public function provideImageDownloadUrl(int $contentId): string
    {
        return ($contentId) ? self::IMAGE_DOWNLOAD_URL : self::EMPTY_IMAGE_DOWNLOAD_URL;
    }
}
