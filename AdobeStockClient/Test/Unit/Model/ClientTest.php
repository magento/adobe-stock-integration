<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Magento\AdobeStockClient\Test\Unit\Model;

use AdobeStock\Api\Models\LicenseEntitlement;
use AdobeStock\Api\Models\LicensePurchaseOptions;
use AdobeStock\Api\Models\LicenseEntitlementQuota;
use AdobeStock\Api\Models\StockFile;
use AdobeStock\Api\Request\License;
use AdobeStock\Api\Response\License as ResponseLicense;
use AdobeStock\Api\Request\LicenseFactory as LicenseRequestFactory;
use AdobeStock\Api\Response\SearchFiles as SearchFilesResponse;
use Magento\AdobeStockClient\Model\Client;
use Magento\AdobeStockClient\Model\ConnectionWrapper;
use Magento\AdobeStockClient\Model\ConnectionWrapperFactory;
use Magento\AdobeStockClient\Model\SearchParameterProviderInterface;
use Magento\AdobeStockClient\Model\StockFileToDocument;
use Magento\AdobeStockClientApi\Api\ConfigInterface;
use Magento\AdobeStockClientApi\Api\Data\LicenseConfirmationInterface;
use Magento\AdobeStockClientApi\Api\Data\LicenseConfirmationInterfaceFactory;
use Magento\AdobeStockClientApi\Api\Data\UserQuotaInterface;
use Magento\AdobeStockClientApi\Api\Data\UserQuotaInterfaceFactory;
use Magento\Framework\Api\Search\Document;
use Magento\Framework\Api\Search\SearchCriteriaInterface;
use Magento\Framework\Api\Search\SearchResultFactory;
use Magento\Framework\Api\Search\SearchResultInterface;
use Magento\Framework\Locale\ResolverInterface as LocaleResolver;
use Magento\Framework\TestFramework\Unit\Helper\ObjectManager;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Psr\Log\LoggerInterface;

/**
 * Config data test.
 */
class ClientTest extends TestCase
{
    private const SEARCH_RESULT_FIELDS = ['ID', 'NB_RESULTS'];

    /**
     * @var MockObject|ConfigInterface $config
     */
    private $config;

    /**
     * @var MockObject|ConnectionWrapperFactory $connectionFactory
     */
    private $connectionFactory;

    /**
     * @var MockObject|SearchResultFactory $searchResultFactory
     */
    private $searchResultFactory;

    /**
     * @var MockObject|SearchParameterProviderInterface $searchParametrProvider
     */
    private $searchParametrProvider;

    /**
     * @var MockObject|LocaleResolver $localeResolver
     */
    private $localeResolver;

    /**
     * @var MockObject|LicenseRequestFactory $licenseRequestFactory
     */
    private $licenseRequestFactory;

    /**
     * @var LoggerInterface|MockObject $logger
     */
    private $logger;

    /**
     * @var MockObject|UserQuotaInterfaceFactory
     */
    private $userQuotaFactory;

    /**
     * @var MockObject|StockFileToDocument $stockFileToDocument
     */
    private $stockFileToDocument;

    /**
     * @var MockObject|LicenseConfirmationInterfaceFactory $licenseConfirmationFactory
     */
    private $licenseConfirmationFactory;

    /**
     * @var Client $client
     */
    private $client;

    /**
     * @var ConnectionWrapper|MockObject
     */
    private $connectionWrapper;

    /**
     * @var ResponseLicense|MockObject $licenseResponse
     */
    private $licenseResponse;

    /**
     * Prepare test objects.
     */
    protected function setUp(): void
    {
        $this->config = $this->createMock(ConfigInterface::class);
        $this->connectionFactory = $this->createMock(ConnectionWrapperFactory::class);
        $this->searchResultFactory = $this->createMock(SearchResultFactory::class);
        $this->searchParametrProvider = $this->createMock(SearchParameterProviderInterface::class);
        $this->localeResolver = $this->createMock(LocaleResolver::class);
        $this->licenseRequestFactory = $this->createMock(LicenseRequestFactory::class);
        $this->logger = $this->createMock(LoggerInterface::class);
        $this->userQuotaFactory = $this->createMock(UserQuotaInterfaceFactory::class);
        $this->stockFileToDocument = $this->createMock(StockFileToDocument::class);
        $this->licenseConfirmationFactory = $this->createMock(LicenseConfirmationInterfaceFactory::class);

        $this->connectionWrapper = $this->createMock(ConnectionWrapper::class);
        $this->connectionFactory->expects($this->any())->method('create')->willReturn($this->connectionWrapper);
        $this->licenseResponse = $this->createMock(ResponseLicense::class);

        $this->client = (new ObjectManager($this))->getObject(
            Client::class,
            [
                'config' => $this->config,
                'connectionFactory' => $this->connectionFactory,
                'searchResultFactory' => $this->searchResultFactory,
                'searchParametersProvider' => $this->searchParametrProvider,
                'localeResolver' => $this->localeResolver,
                'licenseRequestFactory' => $this->licenseRequestFactory,
                'logger' => $this->logger,
                'userQuotaFactory' => $this->userQuotaFactory,
                'stockFileToDocument' => $this->stockFileToDocument,
                'licenseConfirmationFactory' => $this->licenseConfirmationFactory,
                'searchResultFields' => self::SEARCH_RESULT_FIELDS
            ]
        );
    }

    /**
     * Search test
     */
    public function testSearch(): void
    {
        $this->localeResolver->expects($this->once())->method('getLocale')->willReturn('ru_RU');
        $response = $this->createMock(SearchFilesResponse::class);
        $this->connectionWrapper->expects($this->once())->method('getNextResponse')->willReturn($response);
        $response->expects($this->once())->method('getFiles')->willReturn(
            ['file1' => $this->createMock(StockFile::class)]
        );
        $this->stockFileToDocument->expects($this->once())
            ->method('convert')
            ->willReturn(
                $this->createMock(Document::class)
            );

        $response->expects($this->once())->method('getNbResults')->willReturn(12);
        $searchResult = $this->createMock(SearchResultInterface::class);
        $this->searchResultFactory->expects($this->once())
            ->method('create')
            ->willReturn($searchResult);
        $searchResult->expects($this->once())->method('setSearchCriteria')->willReturnSelf();
        $searchResult->expects($this->once())->method('setItems')->willReturnSelf();
        $searchResult->expects($this->once())->method('setTotalCount')->willReturnSelf();

        $this->assertEquals(
            $this->createMock(SearchResultInterface::class),
            $this->client->search($this->createMock(SearchCriteriaInterface::class))
        );
    }

    /**
     * Test get user quota
     */
    public function testGetQuota(): void
    {
        $this->localeResolver->expects($this->once())->method('getLocale')->willReturn('ru_RU');
        $this->connectionWrapper->expects($this->once())
            ->method('getMemberProfile')
            ->willReturn($this->licenseResponse);
        $licenseEntitielement = $this->createMock(LicenseEntitlement::class);
        $this->licenseResponse->expects($this->once())->method('getEntitlement')->willReturn($licenseEntitielement);
        $licenseEntitielement->expects($this->once())
            ->method('getFullEntitlementQuota')
            ->willReturn($this->createMock(LicenseEntitlementQuota::class));
        $this->licenseRequestFactory->expects($this->once())
            ->method('create')
            ->willReturn($this->getLicenseRequest());
        $quota = $this->createMock(UserQuotaInterface::class);
        $this->userQuotaFactory->expects($this->once())
            ->method('create')
            ->willReturn($quota);
        $quota->expects($this->once())->method('setImages')->willReturnSelf();
        $quota->expects($this->once())->method('setCredits')->willReturnSelf();

        $this->assertEquals($this->createMock(UserQuotaInterface::class), $this->client->getQuota());
    }

    /**
     * Get license confirmation test
     */
    public function testGetLicenseConfirmation(): void
    {
        $this->localeResolver->expects($this->once())->method('getLocale')->willReturn('ru_RU');
        $this->connectionWrapper->expects($this->once())
            ->method('getMemberProfile')
            ->willReturn($this->licenseResponse);
        $LicensePurchaseOptions = $this->createMock(LicensePurchaseOptions::class);
        $this->licenseResponse->expects($this->once())
            ->method('getPurchaseOptions')
            ->willReturn($LicensePurchaseOptions);
        $LicensePurchaseOptions->expects($this->once())
            ->method('getMessage')
            ->willReturn('License message');
        $LicensePurchaseOptions->expects($this->once())
            ->method('getPurchaseState')
            ->willReturn('possible');
        $quota = $this->createMock(LicenseConfirmationInterface::class);
        $this->licenseConfirmationFactory->expects($this->once())
            ->method('create')
            ->willReturn($quota);
        $this->licenseRequestFactory->expects($this->once())
            ->method('create')
            ->willReturn($this->getLicenseRequest());
        $quota->expects($this->once())->method('setMessage')->willReturnSelf();
        $quota->expects($this->once())->method('setCanLicense')->willReturnSelf();
        $this->assertEquals(
            $this->createMock(LicenseConfirmationInterface::class),
            $this->client->getLicenseConfirmation(0)
        );
    }

    /**
     * License image test
     */
    public function testLicenseImage(): void
    {
        $this->localeResolver->expects($this->once())->method('getLocale')->willReturn('ru_RU');
        $this->connectionWrapper->expects($this->once())
            ->method('getContentLicense')
            ->willReturn($this->licenseResponse);
        $this->licenseRequestFactory->expects($this->once())
            ->method('create')
            ->willReturn($this->getLicenseRequest());
        $this->client->licenseImage(0);
    }

    /**
     *  Test get image Download Url
     */
    public function testGetImageDownloadUrl(): void
    {
        $this->localeResolver->expects($this->once())->method('getLocale')->willReturn('ru_RU');
        $this->connectionWrapper->expects($this->once())
            ->method('downloadAssetUrl')
            ->willReturn('https://omage.com/png.png');
        $this->licenseRequestFactory->expects($this->once())
            ->method('create')
            ->willReturn($this->getLicenseRequest());
        $this->assertEquals('https://omage.com/png.png', $this->client->getImageDownloadUrl(0));
    }

    /**
     * Test for test connection method
     */
    public function testTestConnection(): void
    {
        $this->connectionWrapper->expects($this->once())
            ->method('testApiKey')
            ->willReturn(true);
        $this->assertEquals(true, $this->client->testConnection('key'));
    }

    /**
     * Retrieve license request
     *
     * @return MockObject
     */
    private function getLicenseRequest(): MockObject
    {
        $licenseRequest = $this->createMock(License::class);
        $licenseRequest->expects($this->once())->method('setContentId')->willReturnSelf();
        $licenseRequest->expects($this->once())->method('setLocale')->willReturnSelf();
        $licenseRequest->expects($this->once())->method('setLicenseState')->willReturnSelf();

        return $licenseRequest;
    }
}
