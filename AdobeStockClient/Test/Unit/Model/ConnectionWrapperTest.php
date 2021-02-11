<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Magento\AdobeStockClient\Test\Unit\Model;

use AdobeStock\Api\Client\AdobeStock;
use AdobeStock\Api\Client\Http\HttpInterface;
use AdobeStock\Api\Request\License as LicenseRequest;
use AdobeStock\Api\Request\SearchFiles as SearchFilesRequest;
use AdobeStock\Api\Response\License as LicenseResponse;
use AdobeStock\Api\Response\SearchFiles;
use Magento\AdobeImsApi\Api\ConfigInterface as ImsConfig;
use Magento\AdobeImsApi\Api\FlushUserTokensInterface;
use Magento\AdobeImsApi\Api\GetAccessTokenInterface;
use Magento\AdobeStockClient\Model\ConnectionFactory;
use Magento\AdobeStockClient\Model\ConnectionWrapper;
use Magento\AdobeStockClientApi\Api\ConfigInterface;
use Magento\Framework\Exception\AuthenticationException;
use Magento\Framework\Exception\AuthorizationException;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

/**
 * Test the Adobe Stock SDK wrapper
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class ConnectionWrapperTest extends TestCase
{
    /**
     * @var ConnectionFactory|MockObject $connectionFactory
     */
    private $connectionFactory;

    /**
     * @var MockObject|ConfigInterface $configInterface
     */
    private $configInterface;

    /**
     * @var MockObject|ImsConfig $imsConfig
     */
    private $imsConfig;

    /**
     * @var MockObject|GetAccessTokenInterface $getAccessToken
     */
    private $getAccessToken;

    /**
     * @var FlushUserTokensInterface|MockObject $flushToken
     */
    private $flushToken;

    /**
     * @var MockObject|HttpInterface $httpInterface
     */
    private $httpInterface;

    /**
     * @var ConnectionWrapper
     */
    private $connectionWrapper;

    /**
     * @var AdobeStock|MockObject $adobeStockMock
     */
    private $adobeStockMock;

    /**
     * Prepare test objects.
     */
    protected function setUp(): void
    {
        $this->connectionFactory = $this->createMock(ConnectionFactory::class);
        $this->configInterface = $this->createMock(ConfigInterface::class);
        $this->imsConfig = $this->createMock(ImsConfig::class);
        $this->getAccessToken = $this->createMock(GetAccessTokenInterface::class);
        $this->flushToken = $this->createMock(FlushUserTokensInterface::class);
        $this->httpInterface = $this->createMock(HttpInterface::class);
        $this->adobeStockMock = $this->createMock(AdobeStock::class);
        $this->connectionFactory->expects($this->once())->method('create')->willReturn($this->adobeStockMock);
        $this->configInterface->expects($this->once())->method('getProductName')->willReturn('name');
        $this->configInterface->expects($this->once())->method('getTargetEnvironment')->willReturn('target');

        $this->connectionWrapper = new ConnectionWrapper(
            $this->configInterface,
            $this->connectionFactory,
            $this->imsConfig,
            $this->getAccessToken,
            $this->flushToken,
            $this->httpInterface
        );
    }

    /**
     * Test is api key can be validated
     */
    public function testApiKey(): void
    {
        $this->adobeStockMock->expects($this->once())->method('searchFilesInitialize')->willReturnSelf();
        $nextResponse = new SearchFiles();
        $nextResponse->setNbResults(12);
        $this->adobeStockMock->expects($this->exactly(1))
            ->method('getNextResponse')
            ->willReturn($nextResponse);
        $this->assertEquals(true, $this->connectionWrapper->testApiKey('kjhafdaeriuyapikey'));
    }

    /**
     * Search file initialize
     */
    public function testSearchFilesInitialize(): void
    {
        $this->imsConfig->expects($this->once())->method('getApiKey')->willReturn('key');
        $searchFileRequest = new SearchFilesRequest();
        $this->adobeStockMock->expects($this->once())
            ->method('searchFilesInitialize')
            ->with($searchFileRequest)
            ->willReturnSelf();
        $this->assertEquals(
            $this->connectionWrapper,
            $this->connectionWrapper->searchFilesInitialize($searchFileRequest)
        );
    }

    /**
     * Search file initialize with exception.
     */
    public function testSearchFilesInitializeException(): void
    {
        $this->expectExceptionMessage('Failed to initialize Adobe Stock search files request: New error');
        $this->imsConfig->expects($this->once())->method('getApiKey')->willReturn('key');
        $searchFileRequest = new SearchFilesRequest();
        $this->adobeStockMock->expects($this->once())
            ->method('searchFilesInitialize')
            ->with($searchFileRequest)
            ->willThrowException(new \Exception('New error'));
        $this->connectionWrapper->searchFilesInitialize($searchFileRequest);
    }

    /**
     * Nest response test
     */
    public function testGetNextResponse(): void
    {
        $this->imsConfig->expects($this->once())->method('getApiKey')->willReturn('key');
        $this->adobeStockMock->expects($this->once())->method('getNextResponse')->willReturn(new SearchFiles());
        $this->assertEquals(new SearchFiles(), $this->connectionWrapper->getNextResponse());
    }

    /**
     * Next response with exception
     */
    public function testGetNextResponseWithException(): void
    {
        $this->expectExceptionMessage('Failed to retrieve Adobe Stock search files results: New error');
        $this->imsConfig->expects($this->once())->method('getApiKey')->willReturn('key');
        $this->adobeStockMock->expects($this->once())
            ->method('getNextResponse')
            ->willThrowException(new \Exception('New error'));
        $this->connectionWrapper->getNextResponse();
    }

    /**
     * Next response with exception that should be explained in more detail
     *
     * @param string $connectionException Exception message thrown by the connection
     * @param string $thrownException Exception message that will throws by the getNextResponse method
     * @param string $exception Exception class that will throws by the getNextResponse method
     *
     * @return void
     * @dataProvider detailedExceptionsForGetNextResponseProvider
     */
    public function testGetNextResponseWithExceptionThatNeedMoreAttention(
        string $connectionException,
        string $thrownException,
        string $exception
    ): void {
        $this->expectException($exception);
        $this->expectExceptionMessage($thrownException);
        $this->imsConfig->method('getApiKey')
            ->willReturn('key');
        $this->adobeStockMock->method('getNextResponse')
            ->willThrowException(new \Exception('Beginning of the exception message ' . $connectionException));
        $this->connectionWrapper->getNextResponse();
    }

    /**
     * Get member profile test
     */
    public function testGetMemberProfile(): void
    {
        $this->setTokens();
        $this->adobeStockMock->expects($this->once())
            ->method('getMemberProfile')
            ->willReturn($this->createMock(LicenseResponse::class));
        $this->connectionWrapper->getMemberProfile(new LicenseRequest());
    }

    /**
     * Get member profile with exception
     */
    public function testGetMemberProfileWithException(): void
    {
        $this->expectExceptionMessage('Failed to retrieve Adobe Stock member profile');
        $this->setTokens();
        $this->adobeStockMock->expects($this->once())
            ->method('getMemberProfile')
            ->willThrowException(new \Exception('New error'));
        $this->assertEquals(
            $this->createMock(LicenseResponse::class),
            $this->connectionWrapper->getMemberProfile(new LicenseRequest())
        );
    }

    /**
     * Test get Content license
     */
    public function testGetContentLicense(): void
    {
        $this->setTokens();
        $this->adobeStockMock->expects($this->once())
            ->method('getContentLicense')
            ->willReturn($this->createMock(LicenseResponse::class));
        $this->assertEquals(
            $this->createMock(LicenseResponse::class),
            $this->connectionWrapper->getContentLicense(new LicenseRequest())
        );
    }

    /**
     * Test get Content license with exception
     */
    public function testGetContentLicenseWithException(): void
    {
        $this->expectExceptionMessage('Failed to retrieve Adobe Stock content license');
        $this->setTokens();
        $this->adobeStockMock->expects($this->once())
            ->method('getContentLicense')
            ->willThrowException(new \Exception('New error'));
        $this->connectionWrapper->getContentLicense(new LicenseRequest());
    }

    /**
     * Download asset url test
     */
    public function testDownloadAssetUrl(): void
    {
        $this->setTokens();
        $this->adobeStockMock->expects($this->once())
            ->method('downloadAssetUrl')
            ->willReturn('url');
        $this->assertEquals('url', $this->connectionWrapper->downloadAssetUrl(new LicenseRequest()));
    }

    /**
     * Download asset url with exception
     */
    public function testDownloadAssetUrlWithExeption(): void
    {
        $this->expectExceptionMessage('Failed to retrieve Adobe Stock asset download URL');
        $this->setTokens();
        $this->adobeStockMock->expects($this->once())
            ->method('downloadAssetUrl')
            ->willThrowException(new \Exception('New error'));
        $this->connectionWrapper->downloadAssetUrl(new LicenseRequest());
    }

    /**
     * If invalid token ensure that tokens flushed
     */
    public function testFlushTokens(): void
    {
        $this->expectExceptionMessage('Adobe API login has expired!');
        $this->setTokens();
        $this->adobeStockMock->expects($this->once())
            ->method('downloadAssetUrl')
            ->willThrowException(new \Exception('Oauth token is not valid!'));
        $this->flushToken->expects($this->once())->method('execute');
        $this->connectionWrapper->downloadAssetUrl(new LicenseRequest());
    }

    /**
     * Provider of exceptions that need more attention in getNextResponse method
     *
     * @return array
     */
    public function detailedExceptionsForGetNextResponseProvider(): array
    {
        return [
            [
                'connection_exception_message' => 'Api Key is invalid',
                'thrown_exception_message' => 'Adobe API Key is invalid!',
                'thrown_exception' => AuthenticationException::class,
            ],
            [
                'connection_exception_message' => 'Api Key is required',
                'thrown_exception_message' => 'Adobe Api Key is required!',
                'thrown_exception' => AuthenticationException::class,
            ],
            [
                'connection_exception_message' => 'Oauth token is not valid',
                'thrown_exception_message' => 'Adobe API login has expired!',
                'thrown_exception' => AuthorizationException::class,
            ],
            [
                'connection_exception_message' => 'Could not validate the oauth token',
                'thrown_exception_message' => 'Adobe API login has expired!',
                'thrown_exception' => AuthorizationException::class,
            ],
        ];
    }

    /**
     * Ste's tokens
     */
    private function setTokens(): void
    {
        $this->imsConfig->expects($this->once())->method('getApiKey')->willReturn('key');
        $this->getAccessToken->expects($this->once())->method('execute')->willReturn('token');
    }
}
