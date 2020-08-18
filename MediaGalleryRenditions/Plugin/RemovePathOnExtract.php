<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Magento\MediaGalleryRenditions\Plugin;

use Magento\MediaContent\Model\ExtractAssetsFromContent;
use Magento\MediaContentApi\Model\Config;

/**
 * Remove rendition directory on content extract
 */
class RemovePathOnExtract
{
    private const RENDITIONS_DIRECTORY_NAME = '.renditions';

    /**
     * @var Config
     */
    private $config;

    public function __construct(Config $config)
    {
        $this->config = $config;
    }

    /**
     * Remove renditions directory from path
     *
     * @param ExtractAssetsFromContent $subject
     * @param string $content
     * @return array
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function beforeExecute(ExtractAssetsFromContent $subject, string $content): array
    {
        if (!$this->config->isEnabled()) {
            return [$content];
        }

        $content = str_replace(
            self::RENDITIONS_DIRECTORY_NAME . '/',
            '',
            $content
        );
        return [$content];
    }
}
