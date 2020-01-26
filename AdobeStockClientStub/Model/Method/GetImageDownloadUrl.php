<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Magento\AdobeStockClientStub\Model\Method;

use Magento\AdobeStockClientStub\Model\DataProvider\ImageDownloadUrl;

/**
 * Provides a stub data for the get image download method of the AdobeStockClient
 */
class GetImageDownloadUrl
{
    /**
     * @var ImageDownloadUrl
     */
    private $imageDownloadUrl;

    /**
     * GetImageDownloadUrl constructor.
     *
     * @param ImageDownloadUrl $imageDownloadUrl
     */
    public function __construct(ImageDownloadUrl $imageDownloadUrl)
    {
        $this->imageDownloadUrl = $imageDownloadUrl;
    }

    /**
     * Return a stub string which emulates the image download url.
     *
     * @param int $contentId
     *
     * @return string
     */
    public function execute(int $contentId): string
    {
        return $this->imageDownloadUrl->provideImageDownloadUrl($contentId);
    }
}
