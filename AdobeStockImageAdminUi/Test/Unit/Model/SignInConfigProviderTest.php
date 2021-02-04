<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

declare(strict_types=1);

namespace Magento\AdobeStockImageAdminUi\Test\Unit\Model;

use Magento\AdobeImsApi\Api\UserAuthorizedInterface;
use Magento\AdobeStockClientApi\Api\ClientInterface;
use Magento\AdobeStockClientApi\Api\Data\UserQuotaInterface;
use Magento\AdobeStockImageAdminUi\Model\SignInConfigProvider;
use Magento\Framework\Exception\AuthenticationException;
use Magento\Framework\Exception\AuthorizationException;
use Magento\Framework\Phrase;
use Magento\Framework\UrlInterface;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

/**
 * SignInConfigProviderTest test.
 */
class SignInConfigProviderTest extends TestCase
{
    /**
     * @var SignInConfigProvider
     */
    private $sut;

    /**
     * @var ClientInterface|MockObject
     */
    private $clientInterfaceMock;

    /**
     * @var UserAuthorizedInterface|MockObject
     */
    private $userAuthorizedMock;

    /**
     * @var UrlInterface|MockObject
     */
    private $urlMock;

    /**
     * @var UserQuotaInterface|MockObject
     */
    private $userQuotaMock;

    /**
     * Set Up
     */
    protected function setUp(): void
    {
        $this->clientInterfaceMock = $this->getMockForAbstractClass(ClientInterface::class);
        $this->userAuthorizedMock = $this->getMockForAbstractClass(UserAuthorizedInterface::class);
        $this->urlMock = $this->getMockForAbstractClass(UrlInterface::class);
        $this->userQuotaMock = $this->getMockForAbstractClass(UserQuotaInterface::class);

        $this->sut = new SignInConfigProvider(
            $this->clientInterfaceMock,
            $this->userAuthorizedMock,
            $this->urlMock
        );
    }

    /**
     * Testing the available quota for for authorized and not authorized users
     *
     * @dataProvider userQuotaProvider
     *
     * @param bool $userIsAuthorized
     * @param array $userQuota
     */
    public function testGettingUserQuota(bool $userIsAuthorized, array $userQuota): void
    {
        $quotaUrl = 'http://site.com/adobe_stock/license/quota';
        $expectedResult = [
            'component' => 'Magento_AdobeStockImageAdminUi/js/signIn',
            'template' => 'Magento_AdobeStockImageAdminUi/signIn',
            'userQuota' => $userQuota,
            'quotaUrl' => $quotaUrl
        ];

        $this->urlMock->expects($this->once())->method('getUrl')->with('adobe_stock/license/quota')
            ->willReturn($quotaUrl);
        $this->userAuthorizedMock->expects($this->once())->method('execute')->willReturn($userIsAuthorized);

        if ($userIsAuthorized) {
            $this->clientInterfaceMock->expects($this->once())->method('getQuota')->willReturn($this->userQuotaMock);
            $this->userQuotaMock->expects($this->once())->method('getImages')->willReturn($userQuota['images']);
            $this->userQuotaMock->expects($this->once())->method('getCredits')->willReturn($userQuota['credits']);
        }

        $result = $this->sut->get();
        $this->assertEquals($result, $expectedResult);
    }

    /**
     * Testing the available quota for for authorized and not authorized users
     *
     * @dataProvider exceptionsDataProvider
     *
     * @param \Exception $exception
     * @param array $userQuota
     */
    public function testGettingUserQuotaOnExceptions(\Exception $exception, array $userQuota): void
    {
        $userIsAuthorized = true;
        $quotaUrl = 'http://site.com/adobe_stock/license/quota';
        $expectedResult = [
            'component' => 'Magento_AdobeStockImageAdminUi/js/signIn',
            'template' => 'Magento_AdobeStockImageAdminUi/signIn',
            'userQuota' => $userQuota,
            'quotaUrl' => $quotaUrl
        ];

        $this->urlMock->expects($this->once())->method('getUrl')->with('adobe_stock/license/quota')
            ->willReturn($quotaUrl);
        $this->userAuthorizedMock->expects($this->once())->method('execute')->willReturn($userIsAuthorized);
        $this->clientInterfaceMock->expects($this->once())->method('getQuota')
            ->willThrowException($exception);

        $result = $this->sut->get();
        $this->assertEquals($result, $expectedResult);
    }

    /**
     * Providing quota for authorized and not authorized users
     *
     * @return array
     */
    public function userQuotaProvider(): array
    {
        return [
            [
                false,
                [
                    'images' => 0,
                    'credits' => 0
                ]
            ], [
                true,
                [
                    'images' => 3,
                    'credits' => 5
                ]
            ]
        ];
    }

    /**
     * Providing the exceptions handling
     *
     * @return array
     */
    public function exceptionsDataProvider(): array
    {
        $defaultQuota = [
            'images' => 0,
            'credits' => 0
        ];

        return [
            [
                new AuthenticationException(new Phrase('Adobe API Key is invalid!')),
                $defaultQuota
            ], [
                new AuthorizationException(new Phrase('Adobe API login has expired!')),
                $defaultQuota
            ]
        ];
    }
}
