<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Magento\MediaGalleryRenditions\Test\Unit\Model;

use Magento\MediaGalleryRenditions\Model\Config;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\TestFramework\Unit\Helper\ObjectManager;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

/**
 * Config data test.
 */
class ConfigTest extends TestCase
{
    private const XML_PATH_MEDIA_GALLERY_RENDITIONS_WIDTH_PATH = 'system/media_gallery_renditions/width';

    private const XML_PATH_MEDIA_GALLERY_RENDITIONS_HEIGHT_PATH = 'system/media_gallery_renditions/height';

    /**
     * @var ObjectManager
     */
    private $objectManager;

    /**
     * @var Config
     */
    private $config;

    /**
     * @var ScopeConfigInterface|MockObject
     */
    private $scopeConfigMock;

    /**
     * Prepare test objects.
     */
    protected function setUp(): void
    {
        $this->objectManager = new ObjectManager($this);
        $this->scopeConfigMock = $this->createMock(ScopeConfigInterface::class);
        $this->config = $this->objectManager->getObject(
            Config::class,
            [
                'scopeConfig' => $this->scopeConfigMock
            ]
        );
    }

    /**
     * Get target environment test.
     */
    public function testGetWidth(): void
    {
        $this->scopeConfigMock->expects($this->once())
            ->method('getValue')
            ->with(self::XML_PATH_MEDIA_GALLERY_RENDITIONS_WIDTH_PATH);
        $this->config->getWidth();
    }

    /**
     * Get product name test.
     */
    public function testGetHeight(): void
    {
        $this->scopeConfigMock->expects($this->once())
            ->method('getValue')
            ->with(self::XML_PATH_MEDIA_GALLERY_RENDITIONS_HEIGHT_PATH);
        $this->config->getHeight();
    }
}
