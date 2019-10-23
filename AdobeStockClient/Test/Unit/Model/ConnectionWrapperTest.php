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
use Magento\AdobeImsApi\Api\Data\ConfigInterface as ImsConfig;
use Magento\AdobeImsApi\Api\FlushUserTokensInterface;
use Magento\AdobeImsApi\Api\GetAccessTokenInterface;
use Magento\AdobeStockClient\Model\ConnectionFactory;
use Magento\AdobeStockClient\Model\ConnectionWrapper;
use Magento\AdobeStockClientApi\Api\Data\ConfigInterface;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Psr\Log\LoggerInterface;

/**
 * Connection wrapper test.
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
     * @var LoggerInterface|MockObject $logger
     */
    private $logger;

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
    public function setUp(): void
    {
        $this->connectionFactory = $this->createMock(ConnectionFactory::class);
        $this->configInterface = $this->createMock(ConfigInterface::class);
        $this->imsConfig = $this->createMock(ImsConfig::class);
        $this->logger = $this->createMock(LoggerInterface::class);
        $this->getAccessToken = $this->createMock(GetAccessTokenInterface::class);
        $this->flushToken = $this->createMock(FlushUserTokensInterface::class);
        $this->httpInterface = $this->createMock(HttpInterface::class);
        $this->adobeStockMock = $adobeStockMock = $this->createMock(AdobeStock::class);
        $this->connectionFactory->expects($this->once())->method('create')->willReturn($this->adobeStockMock);
        $this->configInterface->expects($this->once())->method('getProductName')->willReturn('name');
        $this->configInterface->expects($this->once())->method('getTargetEnvironment')->willReturn('target');

        $this->connectionWrapper = new ConnectionWrapper(
            $this->configInterface,
            $this->connectionFactory,
            $this->imsConfig,
            $this->logger,
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
        $nextResponse = new\AdobeStock\Api\Response\SearchFiles();
        $nextResponse->setNbResults(12);
        $this->adobeStockMock->expects($this->exactly(1))
            ->method('getNextResponse')
            ->willReturn($nextResponse);
        $this->assertEquals(true, $this->connectionWrapper->testApiKey('kjhafdaeriuyapikey'));
    }

    /**
     * Search file initialize
     */
    public function testSearchFilesInitialize()
    {
        $this->imsConfig->expects($this->once())->method('getApiKey')->willReturn('key');
        $searchFileRequest = new SearchFilesRequest();
        $this->getAccessToken->expects($this->once())->method('execute')->willReturn('token');
        $this->adobeStockMock->expects($this->exactly(1))
            ->method('searchFilesInitialize')
            ->with($searchFileRequest, 'token')
            ->willReturnSelf();
        $this->assertEquals(
            $this->connectionWrapper,
            $this->connectionWrapper->searchFilesInitialize($searchFileRequest)
        );
    }

    /**
     * Search file initialize with exception.
     */
    public function testSearchFilesInitializeException()
    {
        $this->expectExceptionMessage('Failed to initialize Adobe Stock search files request: New error');
        $this->imsConfig->expects($this->once())->method('getApiKey')->willReturn('key');
        $this->getAccessToken->expects($this->once())->method('execute')->willReturn('token');
        $searchFileRequest = new SearchFilesRequest();
        $this->adobeStockMock->expects($this->exactly(1))
            ->method('searchFilesInitialize')
            ->with($searchFileRequest, 'token')
            ->willThrowException(new \Exception('New error'));
        $this->connectionWrapper->searchFilesInitialize($searchFileRequest);
    }

    /**
     * Nest response test
     */
    public function testGetNextResponse()
    {
        $this->imsConfig->expects($this->once())->method('getApiKey')->willReturn('key');
        $this->adobeStockMock->expects($this->once())->method('getNextResponse')->willReturn(new SearchFiles());
        $this->assertEquals(new SearchFiles(), $this->connectionWrapper->getNextResponse());
    }

    /**
     * Next response with exception
     */
    public function testGetNextResponseWithException()
    {
        $this->expectExceptionMessage('Failed to retrieve Adobe Stock search files results: New error');
        $this->imsConfig->expects($this->once())->method('getApiKey')->willReturn('key');
        $this->adobeStockMock->expects($this->once())
            ->method('getNextResponse')
            ->willThrowException(new \Exception('New error'));
        $this->connectionWrapper->getNextResponse();
    }

    /**
     * Get member profile test
     */
    public function testGetMemberProfile()
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
    public function testGetMemberProfileWithException()
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
    public function testGetContentLicense()
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
    public function testGetContentLicenseWithException()
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
    public function testDownloadAssetUrl()
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
    public function testDownloadAssetUrlWithExeption()
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
    public function testFlushTokens()
    {
        $this->expectExceptionMessage('Adobe API login has expired!');
        $this->setTokens();
        $this->adobeStockMock->expects($this->once())
            ->method('downloadAssetUrl')
            ->willThrowException(new \Exception('Oauth token is not valid!'));
        $this->flushToken->expects($this->once())->method('execute')->willReturn(true);
        $this->connectionWrapper->downloadAssetUrl(new LicenseRequest());
    }

    /**
     * Ste's tokens
     */
    private function setTokens()
    {
        $this->imsConfig->expects($this->once())->method('getApiKey')->willReturn('key');
        $this->getAccessToken->expects($this->once())->method('execute')->willReturn('token');
    }
}
