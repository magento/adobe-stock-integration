<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Magento\AdobeIms\Test\Unit\Model;

use Magento\AdobeIms\Model\Config;
use Magento\Config\Model\Config\Backend\Admin\Custom;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\UrlInterface;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

/**
 * PHPUnit test for \Magento\AdobeIms\Model\Config
 */
class ConfigTest extends TestCase
{
    /**
     * Constant for string return value
     */
    private const STRING_RETURN = 'STRING_RETURN_VALUE';

    /**
     * @var Config
     */
    private $config;

    /**
     * @var ScopeConfigInterface|MockObject
     */
    private $scopeConfigMock;

    /**
     * @var UrlInterface|MockObject
     */
    private $urlMock;

    /**
     * Set up test mock objects
     */
    protected function setUp(): void
    {
        $this->scopeConfigMock = $this->createMock(ScopeConfigInterface::class);
        $this->urlMock = $this->createMock(UrlInterface::class);

        $this->config = new Config($this->scopeConfigMock, $this->urlMock);
    }

    /**
     * Test for \Magento\AdobeIms\Model\Config::getApiKey
     */
    public function testGetApiKey(): void
    {
        $this->scopeConfigMock->expects($this->once())
            ->method('getValue')
            ->with(Config::XML_PATH_API_KEY)
            ->willReturn(self::STRING_RETURN);

        $this->assertEquals(self::STRING_RETURN, $this->config->getApiKey());
    }

    /**
     * Test for \Magento\AdobeIms\Model\Config::getPrivateKey
     */
    public function testGetPrivateKey(): void
    {
        $this->scopeConfigMock->expects($this->once())
            ->method('getValue')
            ->with(Config::XML_PATH_PRIVATE_KEY)
            ->willReturn(self::STRING_RETURN);

        $this->assertEquals(self::STRING_RETURN, $this->config->getPrivateKey());
    }

    /**
     * Test for \Magento\AdobeIms\Model\Config::getTokenUrl
     */
    public function testGetTokenUrl(): void
    {
        $this->scopeConfigMock->expects($this->once())
            ->method('getValue')
            ->with(Config::XML_PATH_TOKEN_URL)
            ->willReturn(self::STRING_RETURN);

        $this->assertEquals(self::STRING_RETURN, $this->config->getTokenUrl());
    }

    /**
     * Test for \Magento\AdobeIms\Model\Config::getAuthUrl
     */
    public function testGetAuthUrl(): void
    {
        $this->scopeConfigMock->expects($this->exactly(3))
            ->method('getValue')
            ->withConsecutive(
                [Config::XML_PATH_API_KEY],
                [Custom::XML_PATH_GENERAL_LOCALE_CODE],
                [Config::XML_PATH_AUTH_URL_PATTERN]
            )->willReturn(self::STRING_RETURN);

        $this->urlMock->expects($this->once())->method('getUrl')->willReturn(self::STRING_RETURN);

        $this->assertEquals(self::STRING_RETURN, $this->config->getAuthUrl());
    }

    /**
     * Test for \Magento\AdobeIms\Model\Config::getCallBackUrl
     */
    public function testGetCallBackUrl(): void
    {
        $this->urlMock->expects($this->once())
            ->method('getUrl')
            ->with('adobe_ims/oauth/callback')
            ->willReturn(self::STRING_RETURN);

        $this->assertEquals(self::STRING_RETURN, $this->config->getCallBackUrl());
    }

    /**
     * Test for \Magento\AdobeIms\Model\Config::getLogoutUrl
     */
    public function testGetLogoutUrl(): void
    {
        $this->scopeConfigMock->expects($this->once())
            ->method('getValue')
            ->with(Config::XML_PATH_LOGOUT_URL_PATTERN)
            ->willReturn(self::STRING_RETURN);

        $this->assertEquals(self::STRING_RETURN, $this->config->getLogoutUrl(self::STRING_RETURN));
    }

    /**
     * Test for \Magento\AdobeIms\Model\Config::getProfileImageUrl
     */
    public function testGetProfileImageUrl(): void
    {
        $this->scopeConfigMock->expects($this->exactly(2))
            ->method('getValue')
            ->withConsecutive(
                [Config::XML_PATH_API_KEY],
                [Config::XML_PATH_IMAGE_URL_PATTERN]
            )->willReturn(self::STRING_RETURN);

        $this->assertEquals(self::STRING_RETURN, $this->config->getProfileImageUrl());
    }

    /**
     * Test for \Magento\AdobeIms\Model\Config::getDefaultProfileImage
     */
    public function testGetDefaultProfileImage(): void
    {
        $this->scopeConfigMock->expects($this->once())
            ->method('getValue')
            ->with(Config::XML_PATH_DEFAULT_PROFILE_IMAGE)
            ->willReturn(self::STRING_RETURN);

        $this->assertEquals(self::STRING_RETURN, $this->config->getDefaultProfileImage());
    }
}
