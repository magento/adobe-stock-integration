<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Magento\AdobeStockClient\Test\Unit\Model\Client\Files;

use AdobeStock\Api\Client\AdobeStock;
use AdobeStock\Api\Models\StockFile;
use AdobeStock\Api\Request\Files as FilesRequest;
use AdobeStock\Api\Response\Files as FilesResponse;
use Magento\AdobeIms\Model\Config as ImsConfig;
use Magento\AdobeImsApi\Api\GetAccessTokenInterface;
use Magento\AdobeStockClient\Model\Client\Files as AdobeStockFiles;
use Magento\AdobeStockClient\Model\Config as ClientConfig;
use Magento\AdobeStockClient\Model\ConnectionFactory;
use Magento\AdobeStockClient\Model\FilesRequestFactory;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Locale\Resolver;
use Magento\Framework\TestFramework\Unit\Helper\ObjectManager;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Psr\Log\LoggerInterface;

/**
 * Provides unit test for the \Magento\AdobeStockClient\Model\Client\Files
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class FilesTest extends TestCase
{
    /**
     * @var ImsConfig|MockObject
     */
    private $imsConfigMock;

    /**
     * @var ClientConfig|MockObject
     */
    private $clientConfigMock;

    /**
     * @var Resolver|MockObject
     */
    private $localeResolverMock;

    /**
     * @var GetAccessTokenInterface|MockObject
     */
    private $getAccessTokenMock;

    /**
     * @var AdobeStockFiles|MockObject
     */
    private $files;

    /**
     * @var ConnectionFactory|MockObject
     */
    private $connectionFactoryMock;

    /**
     * @var AdobeStock|MockObject
     */
    private $adobeStockMock;

    /**
     * @var FilesRequest|MockObject
     */
    private $filesRequestMock;

    /**
     * @var FilesRequestFactory|MockObject
     */
    private $requestFilesFactoryMock;

    /**
     * @var LoggerInterface|MockObject
     */
    private $loggerMock;

    /**
     * Prepare test objects.
     */
    protected function setUp(): void
    {
        $this->imsConfigMock = $this->createMock(ImsConfig::class);
        $this->clientConfigMock = $this->createMock(ClientConfig::class);
        $this->localeResolverMock = $this->createMock(Resolver::class);
        $this->getAccessTokenMock = $this->createMock(GetAccessTokenInterface::class);
        $this->connectionFactoryMock = $this->createMock(ConnectionFactory::class);
        $this->requestFilesFactoryMock = $this->createMock(FilesRequestFactory::class);
        $this->loggerMock = $this->createMock(LoggerInterface::class);

        $this->files = (new ObjectManager($this))->getObject(
            AdobeStockFiles::class,
            [
                'imsConfig' => $this->imsConfigMock,
                'clientConfig' => $this->clientConfigMock,
                'localeResolver' => $this->localeResolverMock,
                'getAccessToken' => $this->getAccessTokenMock,
                'connectionFactory' => $this->connectionFactoryMock,
                'requestFilesFactory' => $this->requestFilesFactoryMock,
                'logger' => $this->loggerMock,
            ]
        );

        $this->filesRequestMock = $this->createMock(FilesRequest::class);
        $this->adobeStockMock = $this->createMock(AdobeStock::class);
    }

    /**
     * Test when a request was successful but return empty files.
     *
     * @param array $ids
     * @param string $locale
     * @param string $xProductName
     * @param string $targetEnvironment
     * @param string $apiKey
     * @param string $accessToken
     * @param array $columns
     *
     * @dataProvider curlRequestHeaders
     */
    public function testResultContainsEmptyFiles(
        array $ids,
        string $locale,
        string $xProductName,
        string $targetEnvironment,
        string $apiKey,
        string $accessToken,
        array $columns
    ): void {
        $this->imsConfigMock->expects($this->once())
            ->method('getApiKey')
            ->willReturn($apiKey);

        $this->clientConfigMock->expects($this->once())
            ->method('getProductName')
            ->willReturn($xProductName);

        $this->clientConfigMock->expects($this->once())
            ->method('getTargetEnvironment')
            ->willReturn($targetEnvironment);

        $this->connectionFactoryMock->expects($this->once())
            ->method('create')
            ->with($apiKey, $xProductName, $targetEnvironment)
            ->willReturn($this->adobeStockMock);

        $this->requestFilesFactoryMock->expects($this->once())
            ->method('create')
            ->willReturn($this->filesRequestMock);

        $this->filesRequestMock->expects($this->once())
            ->method('setIds')
            ->with($ids)
            ->willReturnSelf();

        $this->filesRequestMock->expects($this->once())
            ->method('setLocale')
            ->with($locale)
            ->willReturnSelf();

        $this->filesRequestMock->expects($this->once())
            ->method('setResultColumns')
            ->with($columns)
            ->willReturnSelf();

        $this->getAccessTokenMock->expects($this->once())
            ->method('execute')
            ->willReturn($accessToken);

        $this->adobeStockMock->expects($this->once())
            ->method('getFiles')
            ->willThrowException(new \Exception('New error'));

        $this->expectException(LocalizedException::class);
        $this->loggerMock->expects($this->once())->method('error');

        $this->files->execute($ids, $columns, $locale);
    }

    /**
     * Test when a request was successful and returned files.
     *
     * @param array $ids
     * @param string $locale
     * @param string $xProductName
     * @param string $targetEnvironment
     * @param string $apiKey
     * @param string $accessToken
     * @param array $columns
     *
     * @dataProvider curlRequestHeaders
     */
    public function testResultContainsFiles(
        array $ids,
        string $locale,
        string $xProductName,
        string $targetEnvironment,
        string $apiKey,
        string $accessToken,
        array $columns
    ): void {
        $this->imsConfigMock->expects($this->once())
            ->method('getApiKey')
            ->willReturn($apiKey);

        $this->clientConfigMock->expects($this->once())
            ->method('getProductName')
            ->willReturn($xProductName);

        $this->clientConfigMock->expects($this->once())
            ->method('getTargetEnvironment')
            ->willReturn($targetEnvironment);

        $this->connectionFactoryMock->expects($this->once())
            ->method('create')
            ->with($apiKey, $xProductName, $targetEnvironment)
            ->willReturn($this->adobeStockMock);

        $this->requestFilesFactoryMock->expects($this->once())
            ->method('create')
            ->willReturn($this->filesRequestMock);

        $this->filesRequestMock->expects($this->once())
            ->method('setIds')
            ->with($ids)
            ->willReturnSelf();

        $this->filesRequestMock->expects($this->once())
            ->method('setLocale')
            ->with($locale)
            ->willReturnSelf();

        $this->filesRequestMock->expects($this->once())
            ->method('setResultColumns')
            ->with($columns)
            ->willReturnSelf();

        $this->getAccessTokenMock->expects($this->once())
            ->method('execute')
            ->willReturn($accessToken);

        $filesResponseMock = $this->createMock(FilesResponse::class);
        $this->adobeStockMock->expects($this->once())
            ->method('getFiles')
            ->with($this->filesRequestMock, $accessToken)
            ->willReturn($filesResponseMock);

        $stockFileMock = $this->createMock(StockFile::class);
        $expectedFiles = [
            [0 => $stockFileMock]
        ];

        $filesResponseMock->expects($this->once())
            ->method('getFiles')
            ->willReturn($expectedFiles);

        $this->assertEquals($expectedFiles, $this->files->execute($ids, $columns, $locale));
    }

    /**
     * Test with exception thrown on empty ids as a parameter of execute.
     */
    public function testWithEmptyIds()
    {
        $this->expectException(LocalizedException::class);
        $this->files->execute([], []);
    }

    /**
     * Data provider for the curl request to the Adobe authentication service
     *
     * @return array
     */
    public function curlRequestHeaders(): array
    {
        return
            [
                [
                    [1],
                    'en',
                    'Magento/dev-2.3-develop',
                    'target-environment',
                    '75y87d439eqweqw4f4asde64ae42060fc571c456sdfsdqwe',
                    'Bearer ' . base64_encode('Magento/dev-2.3-develop'),
                    []
                ]
            ];
    }
}
