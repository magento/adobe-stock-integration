<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Magento\MediaGalleryUi\Block\Adminhtml;

use Magento\Backend\Block\Template;
use Magento\Backend\Block\Template\Context;
use Magento\Framework\Serialize\Serializer\JsonHexTag;

/**
 * Provides required data for the image uploader
 */
class ImageUploader extends Template
{
    const IMAGE_UPLOADER_COMPONENT = 'Magento_MediaGalleryUi/js/image-uploader';
    const ACCEPT_FILE_TYPES = '/(\.|\/)(gif|jpe?g|png)$/i';
    const ALLOWED_EXTENSIONS = 'jpg jpeg png gif';
    const MAX_FILE_SIZE = '2097152';

    /**
     * JsonHexTag Serializer Instance
     *
     * @var JsonHexTag
     */
    private $serializer;

    /**
     * ImageUploader constructor.
     *
     * @param Context $context
     * @param JsonHexTag $json
     * @param array $data
     */
    public function __construct(
        Context $context,
        JsonHexTag $json,
        array $data = []
    ) {
        $this->serializer = $json;
        parent::__construct($context, $data);
    }

    /**
     * Get configuration for UI component
     *
     * @return string
     */
    public function getComponentJsonConfig(): string
    {
        return $this->serializer->serialize(
            $this->getDefaultComponentConfig()
        );
    }

    /**
     * Get default UI component configuration
     *
     * @return array
     */
    private function getDefaultComponentConfig(): array
    {
        return [
            'component' => self::IMAGE_UPLOADER_COMPONENT,
            'config' => [
                'imageUploadUrl' => $this->_urlBuilder->getUrl('media_gallery/image/upload', ['type' => 'image']),
                'acceptFileTypes' => self::ACCEPT_FILE_TYPES,
                'allowedExtensions' => self::ALLOWED_EXTENSIONS,
                'maxFileSize' => self::MAX_FILE_SIZE
            ]
        ];
    }
}
