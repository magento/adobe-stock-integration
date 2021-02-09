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
    private const SERVICE_NAME = 'adobeStockAssetApiAssetRepositoryV1';
    private const RESOURCE_PATH = '/V1/adobestock/asset';

    /**
     * @var ObjectManagerInterface
     */
    private $objectManager;

    /**
     * @var CollectionFactory
     */
    private $assetCollectionFactory;

    /**
     * Set up
     *
     * @return void
     */
    protected function setUp(): void
    {
        $this->objectManager = Bootstrap::getObjectManager();
        $this->assetCollectionFactory = $this->objectManager->get(CollectionFactory::class);
    }

    /**
     * @magentoApiDataFixture ../../../../app/code/Magento/AdobeStockAsset/Test/_files/asset.php
     */
    public function testDelete(): void
    {
        $response = $this->deleteAsset($this->getAssetId());

        if (constant('TESTS_WEB_API_ADAPTER') === self::ADAPTER_REST) {
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
    public function testDeleteWithException(): void
    {
        $notExistedAssetId = -1;
        try {
            $this->deleteAsset($notExistedAssetId);
            $this->fail('Expected throwing exception');
        } catch (\Exception $e) {
            if (constant('TESTS_WEB_API_ADAPTER') === self::ADAPTER_REST) {
                $errorData = $this->processRestExceptionResult($e);
                self::assertEquals($notExistedAssetId, $errorData['parameters'][0]);
                self::assertEquals(Exception::HTTP_NOT_FOUND, $e->getCode());
            } elseif (constant('TESTS_WEB_API_ADAPTER') === self::ADAPTER_SOAP) {
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
                'resourcePath' => self::RESOURCE_PATH . '/' . $assetId,
                'httpMethod'   => Request::HTTP_METHOD_DELETE,
            ],
            'soap' => [
                'service' => self::SERVICE_NAME,
                'operation' => self::SERVICE_NAME . 'DeleteById'
            ],
        ];

        return (constant('TESTS_WEB_API_ADAPTER') === self::ADAPTER_SOAP)
            ? $this->_webApiCall($serviceInfo, ['id' => $assetId])
            : $this->_webApiCall($serviceInfo);
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
