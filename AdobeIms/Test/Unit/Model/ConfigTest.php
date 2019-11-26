<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Magento\AdobeIms\Test\Unit\Model;

use Magento\AdobeIms\Model\Config;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\TestFramework\Unit\Helper\ObjectManager;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

/**
 * Config data test.
 */
class ConfigTest extends TestCase
{
    private const CONFIG_XML_PATH_API_KEY = 'adobe_ims/integration/api_key';

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
     * Get API key test.
     */
    public function testGetApiKey(): void
    {
        $this->scopeConfigMock->expects($this->once())
            ->method('getValue')
            ->with(self::CONFIG_XML_PATH_API_KEY);
        $this->config->getApiKey();
    }
}
