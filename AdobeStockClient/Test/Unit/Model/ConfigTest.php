<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Magento\AdobeStockClient\Test\Unit\Model;

use Magento\AdobeStockClient\Model\Config;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\TestFramework\Unit\Helper\ObjectManager;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

/**
 * Config data test.
 */
class ConfigTest extends TestCase
{
    private const CONFIG_XML_PATH_ENVIRONMENT = 'adobe_stock/integration/environment';

    private const CONFIG_XML_PATH_PRODUCT_NAME = 'adobe_stock/integration/product_name';

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
    public function testGetTargetEnvironment(): void
    {
        $this->scopeConfigMock->expects($this->once())
            ->method('getValue')
            ->with(self::CONFIG_XML_PATH_ENVIRONMENT);
        $this->config->getTargetEnvironment();
    }

    /**
     * Get product name test.
     */
    public function testGetProductName(): void
    {
        $this->scopeConfigMock->expects($this->once())
            ->method('getValue')
            ->with(self::CONFIG_XML_PATH_PRODUCT_NAME);
        $this->config->getProductName();
    }
}
