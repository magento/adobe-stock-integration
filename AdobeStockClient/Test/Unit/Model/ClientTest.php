<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Magento\AdobeStockClient\Test\Unit\Model;

use AdobeStock\Api\Models\LicenseEntitlement;
use AdobeStock\Api\Models\LicensePurchaseOptions;
use AdobeStock\Api\Models\StockFile;
use AdobeStock\Api\Request\License;
use AdobeStock\Api\Request\LicenseFactory as LicenseRequestFactory;
use AdobeStock\Api\Response\SearchFiles as SearchFilesResponse;
use Magento\AdobeStockClient\Model\Client;
use Magento\AdobeStockClient\Model\ConnectionWrapper;
use Magento\AdobeStockClient\Model\ConnectionWrapperFactory;
use Magento\AdobeStockClient\Model\SearchParameterProviderInterface;
use Magento\AdobeStockClient\Model\StockFileToDocument;
use Magento\AdobeStockClientApi\Api\Data\ConfigInterface;
use Magento\AdobeStockClientApi\Api\Data\LicenseConfirmationInterface;
use Magento\AdobeStockClientApi\Api\Data\LicenseConfirmationInterfaceFactory;
use Magento\AdobeStockClientApi\Api\Data\UserQuotaInterface;
use Magento\AdobeStockClientApi\Api\Data\UserQuotaInterfaceFactory;
use Magento\Framework\Api\Search\DocumentInterface;
use Magento\Framework\Api\Search\SearchCriteriaInterface;
use Magento\Framework\Api\Search\SearchResultFactory;
use Magento\Framework\Api\Search\SearchResultInterface;
use Magento\Framework\Locale\ResolverInterface as LocaleResolver;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Psr\Log\LoggerInterface;

/**
 * Config data test.
 */
class ClientTest extends TestCase
{
    /**
     * @var MockObject|ConfigInterface $configInterface
     */
    private $configInterface;

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
    private $userQuotaInterface;

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
     * @var \AdobeStock\Api\Response\License|MockObject $licenseResponse
     */
    private $licenseResponse;

    /**
     * Prepare test objects.
     */
    public function setUp(): void
    {
        $this->configInterface = $this->createMock(ConfigInterface::class);
        $this->connectionFactory = $this->createMock(ConnectionWrapperFactory::class);
        $this->searchResultFactory = $this->createMock(SearchResultFactory::class);
        $this->searchParametrProvider = $this->createMock(SearchParameterProviderInterface::class);
        $this->localeResolver = $this->createMock(LocaleResolver::class);
        $this->licenseRequestFactory = $this->createMock(LicenseRequestFactory::class);
        $this->logger = $this->createMock(LoggerInterface::class);
        $this->userQuotaInterface = $this->createMock(UserQuotaInterfaceFactory::class);
        $this->stockFileToDocument = $this->createMock(StockFileToDocument::class);
        $this->licenseConfirmationFactory = $this->createMock(LicenseConfirmationInterfaceFactory::class);

        $this->connectionWrapper = $this->createMock(\Magento\AdobeStockClient\Model\ConnectionWrapper::class);
        $this->connectionFactory->expects($this->any())->method('create')->willReturn($this->connectionWrapper);
        $this->licenseResponse = $this->createMock(\AdobeStock\Api\Response\License::class);

        $this->client = new Client(
            $this->configInterface,
            $this->connectionFactory,
            $this->searchResultFactory,
            $this->searchParametrProvider,
            $this->localeResolver,
            $this->licenseRequestFactory,
            $this->logger,
            $this->userQuotaInterface,
            $this->stockFileToDocument,
            $this->licenseConfirmationFactory
        );
    }

    /**
     * Search test
     */
    public function testSearch()
    {
        $this->localeResolver->expects($this->once())->method('getLocale')->willReturn('ru_RU');
        $this->configInterface->expects($this->once())
            ->method('getSearchResultFields')
            ->willReturn(['nb_results' => 'NB_RESULTS']);
        $response = $this->createMock(SearchFilesResponse::class);
        $this->connectionWrapper->expects($this->once())->method('getNextResponse')->willReturn($response);
        $response->expects($this->once())->method('getFiles')->willReturn(
            ['file1' => $this->createMock(StockFile::class)]
        );
        $this->stockFileToDocument->expects($this->once())
            ->method('convert')
            ->willReturn(
                $this->createMock(DocumentInterface::class)
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
    public function testGetQuota()
    {
        $this->connectionWrapper->expects($this->once())
            ->method('getMemberProfile')
            ->willReturn($this->licenseResponse);
        $licenseEntitielement = $this->createMock(LicenseEntitlement::class);
        $this->licenseResponse->expects($this->once())->method('getEntitlement')->willReturn($licenseEntitielement);
        $licenseEntitielement->expects($this->once())
            ->method('getFullEntitlementQuota')
            ->willReturn($this->createMock(\AdobeStock\Api\Models\LicenseEntitlementQuota::class));
        $this->setLicense();
        $quota = $this->createMock(\Magento\AdobeStockClientApi\Api\Data\UserQuotaInterface::class);
        $this->userQuotaInterface->expects($this->once())
            ->method('create')
            ->willReturn($quota);
        $quota->expects($this->once())->method('setImages')->willReturnSelf();
        $quota->expects($this->once())->method('setCredits')->willReturnSelf();

        $this->assertEquals($this->createMock(UserQuotaInterface::class), $this->client->getQuota());
    }

    /**
     * Get license confirmation test
     */
    public function testGetLicenseConfirmation()
    {

        $this->connectionWrapper->expects($this->exactly(2))
            ->method('getMemberProfile')
            ->willReturn($this->licenseResponse);
        $LicensePurchaseOptions = $this->createMock(LicensePurchaseOptions::class);
        $this->licenseResponse->expects($this->exactly(2))
            ->method('getPurchaseOptions')
            ->willReturn($LicensePurchaseOptions);
        $LicensePurchaseOptions->expects($this->once())
            ->method('getMessage')
            ->willReturn('License message');
        $LicensePurchaseOptions->expects($this->once())
            ->method('getPurchaseState')
            ->willReturn('possible');
        $quota = $this->createMock(\Magento\AdobeStockClientApi\Api\Data\LicenseConfirmationInterface::class);
        $this->licenseConfirmationFactory->expects($this->once())
            ->method('create')
            ->willReturn($quota);
        $this->setLicense(2);
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
    public function testLicenseImage()
    {
        $this->connectionWrapper->expects($this->once())
            ->method('getContentLicense')
            ->willReturn($this->licenseResponse);
        $this->setLicense();
        $this->client->licenseImage(0);
    }

    /**
     *  Test get image Download Url
     */
    public function testGetImageDownloadUrl()
    {
        $this->connectionWrapper->expects($this->once())
            ->method('downloadAssetUrl')
            ->willReturn('https://omage.com/png.png');
        $this->setLicense();
        $this->assertEquals('https://omage.com/png.png', $this->client->getImageDownloadUrl(0));
    }

    /**
     * Test for test connection method
     */
    public function testTestConnection()
    {
        $this->connectionWrapper->expects($this->once())
            ->method('testApiKey')
            ->willReturn(true);
        $this->assertEquals(true, $this->client->testConnection('key'));
    }

    /**
     * Asserts license request
     *
     * @param int $expectInvocation
     */
    private function setLicense(int $expectInvocation = 1)
    {
        $licenseRequest = $this->createMock(License::class);
        $this->licenseRequestFactory->expects($this->exactly($expectInvocation))
            ->method('create')
            ->willReturn($licenseRequest);
        $licenseRequest->expects($this->exactly($expectInvocation))->method('setContentId')->willReturnSelf();
        $licenseRequest->expects($this->exactly($expectInvocation))->method('setLocale')->willReturnSelf();
        $licenseRequest->expects($this->exactly($expectInvocation))->method('setLicenseState')->willReturnSelf();
    }
}
