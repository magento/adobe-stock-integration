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
     * Constants for test cases
     */
    private const API_KEY = 'API_KEY';
    private const LOCALE_CODE = 'en_US';
    private const PRIVATE_KEY = 'PRIVATE_KEY';
    private const REDIRECT_URI = 'REDIRECT_URI';
    private const ACCCESS_TOKEN = 'ACCCESS_TOKEN';
    private const TOKEN_URL = 'https://token-url.com/integration';
    private const IMAGE_URL_DEFAULT = 'https://image-url.com/default';
    private const CALLBACK_URL = 'https://magento-instance.com/adobe_ims/oauth/callback';
    private const IMAGE_URL_PATTERN = 'https://image-url.com/pattern?api_key=#{api_key}';
    private const LOGOUT_URL_PATTERN = 'https://logout-url.com/pattern?access_token=#{access_token}&redirect_uri=#{redirect_uri}';
    private const AUTH_URL_PATTERN = 'https://auth-url.com/pattern?client_id=#{client_id}&redirect_uri=#{redirect_uri}&locale=#{locale}';

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
        $this->scopeConfigMock->method('getValue')
            ->with(Config::XML_PATH_API_KEY)
            ->willReturn(self::API_KEY);

        $this->assertEquals(self::API_KEY, $this->config->getApiKey());
    }

    /**
     * Test for \Magento\AdobeIms\Model\Config::getPrivateKey
     */
    public function testGetPrivateKey(): void
    {
        $this->scopeConfigMock->method('getValue')
            ->with(Config::XML_PATH_PRIVATE_KEY)
            ->willReturn(self::PRIVATE_KEY);

        $this->assertEquals(self::PRIVATE_KEY, $this->config->getPrivateKey());
    }

    /**
     * Test for \Magento\AdobeIms\Model\Config::getTokenUrl
     */
    public function testGetTokenUrl(): void
    {
        $this->scopeConfigMock->method('getValue')
            ->with(Config::XML_PATH_TOKEN_URL)
            ->willReturn(self::TOKEN_URL);

        $this->assertEquals(self::TOKEN_URL, $this->config->getTokenUrl());
    }

    /**
     * Test for \Magento\AdobeIms\Model\Config::getAuthUrl
     */
    public function testGetAuthUrl(): void
    {
        $this->scopeConfigMock->method('getValue')
            ->willReturnMap([
                [Config::XML_PATH_API_KEY, ScopeConfigInterface::SCOPE_TYPE_DEFAULT, null, self::API_KEY],
                [Custom::XML_PATH_GENERAL_LOCALE_CODE, ScopeConfigInterface::SCOPE_TYPE_DEFAULT, null, self::LOCALE_CODE],
                [Config::XML_PATH_AUTH_URL_PATTERN, ScopeConfigInterface::SCOPE_TYPE_DEFAULT, null, self::AUTH_URL_PATTERN]
            ]);

        $this->urlMock->method('getUrl')->willReturn(self::CALLBACK_URL);

        $this->assertEquals('https://auth-url.com/pattern?client_id=' . self::API_KEY . '&redirect_uri=' . self::CALLBACK_URL . '&locale=' . self::LOCALE_CODE, $this->config->getAuthUrl());
    }

    /**
     * Test for \Magento\AdobeIms\Model\Config::getCallBackUrl
     */
    public function testGetCallBackUrl(): void
    {
        $this->urlMock->method('getUrl')
            ->with('adobe_ims/oauth/callback')
            ->willReturn(self::CALLBACK_URL);

        $this->assertEquals(self::CALLBACK_URL, $this->config->getCallBackUrl());
    }

    /**
     * Test for \Magento\AdobeIms\Model\Config::getLogoutUrl
     */
    public function testGetLogoutUrl(): void
    {
        $this->scopeConfigMock->method('getValue')
            ->with(Config::XML_PATH_LOGOUT_URL_PATTERN)
            ->willReturn(self::LOGOUT_URL_PATTERN);

        $this->assertEquals('https://logout-url.com/pattern?access_token=' . self::ACCCESS_TOKEN . '&redirect_uri=' . self::REDIRECT_URI, $this->config->getLogoutUrl(self::ACCCESS_TOKEN, self::REDIRECT_URI));
    }

    /**
     * Test for \Magento\AdobeIms\Model\Config::getProfileImageUrl
     */
    public function testGetProfileImageUrl(): void
    {
        $this->scopeConfigMock->method('getValue')
            ->willReturnMap([
                [Config::XML_PATH_IMAGE_URL_PATTERN, ScopeConfigInterface::SCOPE_TYPE_DEFAULT, null, self::IMAGE_URL_PATTERN],
                [Config::XML_PATH_API_KEY, ScopeConfigInterface::SCOPE_TYPE_DEFAULT, null, self::API_KEY]
            ]);

        $this->assertEquals('https://image-url.com/pattern?api_key=' . self::API_KEY, $this->config->getProfileImageUrl());
    }

    /**
     * Test for \Magento\AdobeIms\Model\Config::getDefaultProfileImage
     */
    public function testGetDefaultProfileImage(): void
    {
        $this->scopeConfigMock->method('getValue')
            ->with(Config::XML_PATH_DEFAULT_PROFILE_IMAGE)
            ->willReturn(self::IMAGE_URL_DEFAULT);

        $this->assertEquals(self::IMAGE_URL_DEFAULT, $this->config->getDefaultProfileImage());
    }
}
