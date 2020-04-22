<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

declare(strict_types=1);

namespace Magento\AdobeStockAsset\Test\Api\AssetRepository;

use Magento\Framework\Webapi\Exception;
use Magento\Framework\Webapi\Rest\Request;
use Magento\TestFramework\TestCase\WebapiAbstract;

/**
 * Test getting and Adobe Stock asset by id through the Web API.
 */
class GetByIdTest extends WebapiAbstract
{
    private const FIXTURE_ASSET_ID = 1;
    private const RESOURCE_PATH = '/V1/adobestock/asset';
    private const SERVICE_NAME = 'adobeStockAssetApiAssetRepositoryV1';
    private const SERVICE_OPERATION = 'GetById';

    /**
     * Test get asset by id API with NoSuchEntityException
     *
     * @return void
     * @throws \Exception
     */
    public function testGetNoSuchEntityException(): void
    {
        $notExistedAssetId = 1;
        $serviceInfo = [
            'rest' => [
                'resourcePath' => self::RESOURCE_PATH . DIRECTORY_SEPARATOR . $notExistedAssetId,
                'httpMethod' => Request::HTTP_METHOD_GET,
            ],
            'soap' => [
                'service' => self::SERVICE_NAME,
                'operation' => self::SERVICE_NAME . self::SERVICE_OPERATION
            ],
        ];

        $expectedMessage = 'Adobe Stock asset with id %id does not exist.';
        try {
            if (constant('TESTS_WEB_API_ADAPTER') === self::ADAPTER_REST) {
                $this->_webApiCall($serviceInfo);
            } else {
                $this->_webApiCall($serviceInfo, ['id' => $notExistedAssetId]);
            }
            $this->fail('Expected throwing exception');
        } catch (\Exception $e) {
            if (constant('TESTS_WEB_API_ADAPTER') === self::ADAPTER_REST) {
                $errorData = $this->processRestExceptionResult($e);
                self::assertEquals($expectedMessage, $errorData['message']);
                self::assertEquals($notExistedAssetId, $errorData['parameters']['id']);
                self::assertEquals(Exception::HTTP_NOT_FOUND, $e->getCode());
            } elseif (constant('TESTS_WEB_API_ADAPTER') === self::ADAPTER_SOAP) {
                $this->assertInstanceOf('SoapFault', $e);
                $this->checkSoapFault($e, $expectedMessage, 'env:Sender', [1 => $notExistedAssetId]);
            } else {
                throw $e;
            }
        }
    }

    /**
     * Test get by ID
     *
     * @magentoApiDataFixture ../../../../app/code/Magento/AdobeStockAsset/Test/_files/asset.php
     *
     * @return void
     */
    public function testGetAssetById(): void
    {
        $serviceInfo = [
            'rest' => [
                'resourcePath' => self::RESOURCE_PATH . '/' . self::FIXTURE_ASSET_ID,
                'httpMethod' => Request::HTTP_METHOD_GET,
            ],
            'soap' => [
                'service' => self::SERVICE_NAME,
                'operation' => self::SERVICE_NAME . self::SERVICE_OPERATION,
            ],
        ];

        if (constant('TESTS_WEB_API_ADAPTER') === self::ADAPTER_REST) {
            $assetResultData = $this->_webApiCall($serviceInfo);
        } else {
            $assetResultData = $this->_webApiCall($serviceInfo, ['id' => self::FIXTURE_ASSET_ID]);
        }

        self::assertEquals(self::FIXTURE_ASSET_ID, $assetResultData['id']);
    }
}
