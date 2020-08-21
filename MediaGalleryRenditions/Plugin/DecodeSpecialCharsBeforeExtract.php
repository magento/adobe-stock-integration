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
 * Decode Html Special Characters before matching
 */
class DecodeSpecialCharsBeforeExtract
{
    /**
     * @var Config
     */
    private $config;

    /**
     * DecodeSpecialCharsBeforeExtract constructor.
     *
     * @param Config $config
     */
    public function __construct(Config $config)
    {
        $this->config = $config;
    }

    /**
     *  Decode html special characters from content to
     * accurately match with media_content.xml patterns
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

        return [htmlspecialchars_decode($content)];
    }
}
