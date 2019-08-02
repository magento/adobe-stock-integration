<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Magento\AdobeStockAsset\Test\Api\AssetRepository;

use Magento\AdobeStockAsset\Model\ResourceModel\Asset\CollectionFactory;
use Magento\AdobeStockAsset\Model\ResourceModel\Asset\Collection;
use Magento\AdobeStockAssetApi\Api\Data\AssetInterface;
use Magento\Framework\ObjectManagerInterface;
use Magento\Framework\Webapi\Exception;
use Magento\Framework\Webapi\Rest\Request;
use Magento\TestFramework\Helper\Bootstrap;
use Magento\TestFramework\TestCase\WebapiAbstract;

/**
 * Testing delete asset web api
 */
class DeleteTest extends WebapiAbstract
{
    /**
     * Service name
     */
    private const SERVICE_NAME = 'adobeStockAssetApiAssetRepositoryV1';

    /**
     * Service version
     */
    private const SERVICE_VERSION = 'V1';

    /**
     * Resource path
     */
    private const RESOURCE_PATH = '/V1/adobestock/asset';

    /**
     * Service operation
     */
    private const SERVICE_OPERATION = 'DeleteById';

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
     * @magentoApiDataFixture assetFixtureProvider
     */
    public function testDelete()
    {
        $response = $this->deleteAsset($this->getAssetId());

        if (TESTS_WEB_API_ADAPTER === self::ADAPTER_REST) {
            $this->assertSame([], $response);
        } else {
            $this->assertNull($response);
        }
    }

    /**
     * Test delete assert with exception
     *
     * @return void
     * @throws \Exception
     */
    public function testDeleteWithException()
    {
        try {
            $notExistedAssetId = -1;
            $this->deleteAsset($notExistedAssetId);
            $this->fail('Expected throwing exception');
        } catch (\Exception $e) {
            if (TESTS_WEB_API_ADAPTER === self::ADAPTER_REST) {
                $errorData = $this->processRestExceptionResult($e);
                self::assertEquals($notExistedAssetId, $errorData['parameters'][0]);
                self::assertEquals(Exception::HTTP_NOT_FOUND, $e->getCode());
            } elseif (TESTS_WEB_API_ADAPTER === self::ADAPTER_SOAP) {
                $this->assertInstanceOf('SoapFault', $e);
            } else {
                throw $e;
            }
        }
    }

    /**
     * Delete Adobe Stock Asset
     *
     * @param int $assetId
     * @return array|bool|float|int|string
     */
    private function deleteAsset(int $assetId)
    {
        $serviceInfo = [
            'rest' => [
                'resourcePath' => self::RESOURCE_PATH . DIRECTORY_SEPARATOR . $assetId,
                'httpMethod'   => Request::HTTP_METHOD_DELETE,
            ],
            'soap' => [
                'service' => self::SERVICE_NAME,
                'serviceVersion' => self::SERVICE_VERSION,
                'operation' => self::SERVICE_NAME . self::SERVICE_OPERATION
            ],
        ];

        return (TESTS_WEB_API_ADAPTER === self::ADAPTER_SOAP) ?
            $this->_webApiCall($serviceInfo, [AssetInterface::ID => $assetId])
            : $this->_webApiCall($serviceInfo);
    }

    /**
     * Asset fixture provider
     *
     * @return void
     */
    public static function assetFixtureProvider()
    {
        require __DIR__ . '/../../_files/asset.php';
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
}
