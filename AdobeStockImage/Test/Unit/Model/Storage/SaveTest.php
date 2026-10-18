<?php
/**
 * Copyright 2024 Adobe
 * All Rights Reserved.
 */
declare(strict_types=1);

namespace Magento\AdobeStockImage\Test\Unit\Model\Storage;

use Laminas\Http\Client\Adapter\Test as TestAdapter;
use Laminas\Http\Response;
use Magento\AdobeStockImage\Model\Storage\Save;
use Magento\Framework\App\Filesystem\DirectoryList;
use Magento\Framework\Exception\AlreadyExistsException;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Filesystem;
use Magento\Framework\Filesystem\Directory\Write;
use Magento\Framework\Filesystem\Driver\Https;
use Magento\Framework\HTTP\LaminasClient;
use Magento\Framework\HTTP\LaminasClientFactory;
use Magento\MediaGalleryApi\Api\IsPathExcludedInterface;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

/**
 * Test for the storage save functionality
 */
class SaveTest extends TestCase
{
    /**
     * @var MockObject | Write
     */
    private $mediaDirectoryMock;

    /**
     * @var Save
     */
    private $save;

    /**
     * @var MockObject | Filesystem
     */
    public $fileSystemMock;

    /**
     * @var MockObject | LaminasClient
     */
    private $clientMock;

    /**
     * @var MockObject | Response
     */
    private $responseMock;

    /**
     * @var MockObject | LaminasClientFactory
     */
    private $laminasClientFactoryMock;

    /**
     * Initialize base test objects
     */
    protected function setUp(): void
    {
        $this->fileSystemMock = $this->createMock(Filesystem::class);
        $this->clientMock = $this->createMock(LaminasClient::class);
        $this->responseMock = $this->createMock(Response::class);
        $this->laminasClientFactoryMock = $this->createMock(LaminasClientFactory::class);
        $this->mediaDirectoryMock = $this->createMock(Write::class);

        $this->save = new Save(
            $this->fileSystemMock,
            $this->createStub(Https::class),
            $this->createStub(IsPathExcludedInterface::class),
            null,
            $this->laminasClientFactoryMock
        );
    }

    /**
     * Test image preview save
     */
    public function testSavePreview(): void
    {
        $imageUrl = 'https://t4.ftcdn.net/jpg/02/72/29/99/240_F_272299924_HjNOJkyyhzFVKRcSQ2TaArR7Ka6nTXRa.jpg';

        $this->fileSystemMock->expects($this->once())
            ->method('getDirectoryWrite')
            ->with(DirectoryList::MEDIA)
            ->willReturn($this->mediaDirectoryMock);

        $this->mediaDirectoryMock->expects($this->once())
            ->method('isExist')
            ->withAnyParameters()
            ->willReturn(false);
        $this->expectDownload($imageUrl, [200]);
        $this->responseMock->expects($this->once())->method('getBody')->willReturn('content');

        $this->mediaDirectoryMock->expects($this->once())
            ->method('writeFile')
            ->with('/240_F_272299924_HjNOJkyyhzFVKRcSQ2TaArR7Ka6nTXRa.jpg', 'content')
            ->willReturn(1);

        $this->save->execute($imageUrl, '/240_F_272299924_HjNOJkyyhzFVKRcSQ2TaArR7Ka6nTXRa.jpg');
    }

    /**
     * Assume that save action will thrown an Exception
     */
    public function testExceptionOnSaveExecution(): void
    {
        $imageUrl = 'https://t4.ftcdn.net/jpg/02/72/29/99/240_F_272299924_HjNOJkyyhzFVKRcSQ2TaArR7Ka6nTXRa.jpg';

        $this->fileSystemMock->expects($this->once())
            ->method('getDirectoryWrite')
            ->with(DirectoryList::MEDIA)
            ->willReturn($this->mediaDirectoryMock);
        $this->mediaDirectoryMock->expects($this->once())
            ->method('isExist')
            ->withAnyParameters()
            ->willReturn(true);

        $this->laminasClientFactoryMock->expects($this->never())->method('create');
        $this->clientMock->expects($this->never())->method('send');

        $this->mediaDirectoryMock->expects($this->never())
            ->method('writeFile');

        $this->expectException(AlreadyExistsException::class);

        $this->save->execute($imageUrl, '/240_F_272299924_HjNOJkyyhzFVKRcSQ2TaArR7Ka6nTXRa.jpg');
    }

    /**
     * @return array
     */
    public static function successfulResponseProvider(): array
    {
        return [
            'direct download' => [[200]],
            'CDN redirect' => [[302, 200]],
            'multiple redirects' => [[301, 302, 200]]
        ];
    }

    /**
     * Reject response bodies unless the final status is 200.
     *
     * @param int[] $statuses
     * @param int $expectedStatus
     */
    #[DataProvider('failedResponseProvider')]
    public function testNon200Response(array $statuses, int $expectedStatus): void
    {
        $imageUrl = 'https://example.com/image.jpg';
        $this->fileSystemMock->method('getDirectoryWrite')->willReturn($this->mediaDirectoryMock);
        $this->mediaDirectoryMock->method('isExist')->willReturn(false);
        $this->expectDownload($imageUrl, $statuses);
        $this->responseMock->expects($this->never())->method('getBody');
        $this->mediaDirectoryMock->expects($this->never())->method('writeFile');

        $this->expectException(LocalizedException::class);
        $this->expectExceptionMessage('Could not save image: HTTP status ' . $expectedStatus . '.');

        $this->save->execute($imageUrl, '/image.jpg');
    }

    /**
     * @return array
     */
    public static function failedResponseProvider(): array
    {
        return [
            'not found' => [[404], 404],
            'server error' => [[500], 500],
            'empty success' => [[204], 204],
            'unresolved redirect' => [[302], 302],
            'CDN error' => [[302, 404], 404]
        ];
    }

    /**
     * Preserve the download exception without writing a response body.
     *
     * @param string $failingMethod
     */
    #[DataProvider('downloadExceptionProvider')]
    public function testTransportException(string $failingMethod): void
    {
        $imageUrl = 'https://example.com/image.jpg';
        if ($failingMethod === 'setUri') {
            $imageUrl = 'http://[invalid';
            $exception = new \InvalidArgumentException('Invalid URL');
        } else {
            $exception = new \Exception('Connection timed out');
        }
        $this->fileSystemMock->method('getDirectoryWrite')->willReturn($this->mediaDirectoryMock);
        $this->mediaDirectoryMock->method('isExist')->willReturn(false);
        $this->laminasClientFactoryMock->expects($this->once())->method('create')->willReturn($this->clientMock);
        if ($failingMethod === 'setUri') {
            $this->clientMock->expects($this->once())->method('setUri')
                ->with($imageUrl)->willThrowException($exception);
            $this->clientMock->expects($this->never())->method('send');
        } else {
            $this->clientMock->expects($this->once())->method('setUri')->with($imageUrl);
            $this->clientMock->expects($this->once())->method('send')->willThrowException($exception);
        }
        $this->responseMock->expects($this->never())->method('getBody');
        $this->mediaDirectoryMock->expects($this->never())->method('writeFile');

        try {
            $this->save->execute($imageUrl, '/image.jpg');
            $this->fail('Expected a localized download exception.');
        } catch (LocalizedException $actual) {
            $this->assertSame('Could not save image: download failed.', $actual->getMessage());
            $this->assertSame($exception, $actual->getPrevious());
        }
    }

    /**
     * @param string $imageUrl
     * @param int[] $statuses
     */
    private function expectDownload(string $imageUrl, array $statuses): void
    {
        $this->laminasClientFactoryMock->expects($this->once())->method('create')->willReturn($this->clientMock);
        $this->clientMock->expects($this->once())->method('setOptions')->with([
            'timeout' => 0,
            'maxredirects' => 5,
            'storeresponse' => false,
            CURLOPT_CONNECTTIMEOUT => 10,
            CURLOPT_LOW_SPEED_LIMIT => 1,
            CURLOPT_LOW_SPEED_TIME => 60
        ]);
        $this->clientMock->expects($this->once())->method('setUri')->with($imageUrl);
        $this->clientMock->expects($this->once())->method('setMethod')->with('GET');
        $this->clientMock->expects($this->once())->method('send')->willReturn($this->responseMock);
        $this->responseMock->method('getStatusCode')->willReturn($statuses[array_key_last($statuses)]);
    }

    /**
     * @return array
     */
    public static function downloadExceptionProvider(): array
    {
        return [
            'transport failure' => ['send'],
            'malformed URL' => ['setUri']
        ];
    }

    /**
     * Exercise final responses and client-level redirects without network access.
     *
     * @param int[] $statuses
     * @param int $expectedStatus
     */
    #[DataProvider('successfulResponseProvider')]
    #[DataProvider('failedResponseProvider')]
    public function testClientRedirects(array $statuses, int $expectedStatus = 200): void
    {
        $responses = [];
        foreach ($statuses as $index => $status) {
            $location = isset($statuses[$index + 1])
                ? 'Location: https://cdn.example.com/' . ($index + 1) . ".jpg\r\n"
                : '';
            $responses[] = 'HTTP/1.1 ' . $status . " Response\r\n" . $location . "\r\ncontent";
        }
        $adapter = new TestAdapter();
        $adapter->setResponse($responses);
        $client = new LaminasClient();
        $client->setAdapter($adapter);
        $this->laminasClientFactoryMock->expects($this->once())->method('create')->willReturn($client);
        $this->fileSystemMock->method('getDirectoryWrite')->willReturn($this->mediaDirectoryMock);
        $this->mediaDirectoryMock->method('isExist')->willReturn(false);
        if ($expectedStatus === 200) {
            $this->mediaDirectoryMock->expects($this->once())->method('writeFile')->with('/image.jpg', 'content');
        } else {
            $this->mediaDirectoryMock->expects($this->never())->method('writeFile');
            $this->expectException(LocalizedException::class);
            $this->expectExceptionMessage('Could not save image: HTTP status ' . $expectedStatus . '.');
        }

        try {
            $this->save->execute('https://example.com/image.jpg', '/image.jpg');
        } finally {
            $this->assertSame(count($statuses) - 1, $client->getRedirectionsCount());
            $this->assertSame($expectedStatus, $client->getResponse()->getStatusCode());
            $this->assertNull($client->getLastRawResponse());
        }
    }
}
