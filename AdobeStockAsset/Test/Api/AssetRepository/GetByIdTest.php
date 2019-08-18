<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

declare(strict_types=1);

namespace Magento\AdobeStockAsset\Test\Api\AssetRepository;

use Magento\AdobeStockAsset\Model\ResourceModel\Asset\Collection;
use Magento\AdobeStockAsset\Model\ResourceModel\Asset\CollectionFactory;
use Magento\AdobeStockAssetApi\Api\Data\AssetInterface;
use Magento\Framework\ObjectManagerInterface;
use Magento\Framework\Webapi\Exception;
use Magento\Framework\Webapi\Rest\Request;
use Magento\TestFramework\Helper\Bootstrap;
use Magento\TestFramework\TestCase\WebapiAbstract;

/**
 * Class GetByIdTest
 */
class GetByIdTest extends WebapiAbstract
{
    /**
     * Resource path
     */
    const RESOURCE_PATH = '/V1/adobestock/asset';

    /**
     * Service version
     */
    const SERVICE_VERSION = 'V1';

    /**
     * Service name
     */
    const SERVICE_NAME = 'adobeStockAssetApiAssetRepositoryV1';

    /**
     * Service operation
     */
    const SERVICE_OPERATION = 'GetById';

    /**
     * @var ObjectManagerInterface
     */
    private $objectManager;

    /**
     * @var Collection
     */
    private $assetCollectionFactory;

    /**
     * Set up
     *
     * @return void
     */
    public function setUp()
    {
        $this->objectManager = Bootstrap::getObjectManager();
        $this->assetCollectionFactory = $this->objectManager->get(CollectionFactory::class);
    }

    /**
     * Test get asset by id API with NoSuchEntityException
     *
     * @return void
     * @throws \Exception
     */
    public function testGetNoSuchEntityException()
    {
        $notExistedAssetId = -1;
        $serviceInfo = [
            'rest' => [
                'resourcePath' => self::RESOURCE_PATH . DIRECTORY_SEPARATOR . $notExistedAssetId,
                'httpMethod' => Request::HTTP_METHOD_GET,
            ],
            'soap' => [
                'service' => self::SERVICE_NAME,
                'serviceVersion' => self::SERVICE_VERSION,
                'operation' => self::SERVICE_NAME . self::SERVICE_OPERATION
            ],
        ];

        $expectedMessage = 'Object with id "%1" does not exist.';

        try {
            if (TESTS_WEB_API_ADAPTER === self::ADAPTER_REST) {
                $this->_webApiCall($serviceInfo);
            } else {
                $this->_webApiCall($serviceInfo, [AssetInterface::ID => $notExistedAssetId,]);
            }
            $this->fail('Expected throwing exception');
        } catch (\Exception $e) {
            if (TESTS_WEB_API_ADAPTER === self::ADAPTER_REST) {
                $errorData = $this->processRestExceptionResult($e);
                self::assertEquals($expectedMessage, $errorData['message']);
                self::assertEquals($notExistedAssetId, $errorData['parameters'][0]);
                self::assertEquals(Exception::HTTP_NOT_FOUND, $e->getCode());
            } elseif (TESTS_WEB_API_ADAPTER === self::ADAPTER_SOAP) {
                $this->assertInstanceOf('SoapFault', $e);
                $this->checkSoapFault($e, $expectedMessage, 'env:Sender', [1 => $notExistedAssetId,]);
            } else {
                throw $e;
            }
        }
    }

    /**
     * Test get by ID
     *
     * @magentoApiDataFixture Magento_AdobeStockAsset/_files/assets.php
     *
     * @return void
     */
    public function testGetAssetById()
    {
        $assetId = $this->getAssetId();
        $serviceInfo = [
            'rest' => [
                'resourcePath' => self::RESOURCE_PATH . '/' . $assetId,
                'httpMethod' => Request::HTTP_METHOD_GET,
            ],
            'soap' => [
                'service' => self::SERVICE_NAME,
                'serviceVersion' => self::SERVICE_VERSION,
                'operation' => self::SERVICE_NAME . self::SERVICE_OPERATION,
            ],
        ];

        if (TESTS_WEB_API_ADAPTER === self::ADAPTER_REST) {
            $assetResultData = $this->_webApiCall($serviceInfo);
        } else {
            $assetResultData = $this->_webApiCall($serviceInfo, [AssetInterface::ID => $assetId,]);
        }

        self::assertEquals($assetId, $assetResultData[AssetInterface::ID]);
    }

    /**
     * Get asset id
     *
     * @return int
     */
    private function getAssetId(): int
    {
        /** @var Collection $collection */
        $collection = $this->assetCollectionFactory->create();
        /** @var AssetInterface $asset */
        $asset = $collection->getLastItem();

        return (int) $asset->getId();
    }

    /**
     * Asset fixture provider
     *
     * @return void
     */
    public static function assetFixtureProvider()
    {
        require __DIR__ . '/../../_files/api_assets.php';
    }
}
