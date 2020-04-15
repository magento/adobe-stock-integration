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
 * Testing deleting and Adobe Stock asset by id through the WEB API.
 */
class DeleteByIdTest extends WebapiAbstract
{
    private const FIXTURE_ASSET_ID = 1;
    private const SERVICE_NAME = 'adobeStockAssetApiAssetRepositoryV1';
    private const RESOURCE_PATH = '/V1/adobestock/asset';
    private const SERVICE_OPERATION_DELETE_BY_ID = 'DeleteById';
    private const SERVICE_OPERATION_GET_BY_ID = 'GetById';

    /**
     * @magentoApiDataFixture ../../../../app/code/Magento/AdobeStockAsset/Test/_files/asset.php
     */
    public function testDelete(): void
    {
        $response = $this->deleteAsset(self::FIXTURE_ASSET_ID);

        if (constant('TESTS_WEB_API_ADAPTER') === self::ADAPTER_REST) {
            $this->assertSame([], $response);
        } else {
            $this->assertNull($response);
        }

        $this->verifyAssetDeleted();
    }

    /**
     * Verify that an asset does not exists in a data storage after delete request executed.
     */
    private function verifyAssetDeleted(): void
    {
        $serviceInfo = [
            'rest' => [
                'resourcePath' => self::RESOURCE_PATH . DIRECTORY_SEPARATOR . self::FIXTURE_ASSET_ID,
                'httpMethod' => Request::HTTP_METHOD_GET,
            ],
            'soap' => [
                'service' => self::SERVICE_NAME,
                'operation' => self::SERVICE_NAME . self::SERVICE_OPERATION_GET_BY_ID
            ],
        ];

        $expectedMessage = 'Adobe Stock asset with id %id does not exist.';
        try {
            if (constant('TESTS_WEB_API_ADAPTER') === self::ADAPTER_REST) {
                $this->_webApiCall($serviceInfo);
            } else {
                $this->_webApiCall($serviceInfo, ['id' => self::FIXTURE_ASSET_ID]);
            }
            $this->fail('Expected throwing exception');
        } catch (\Exception $e) {
            if (constant('TESTS_WEB_API_ADAPTER') === self::ADAPTER_REST) {
                $errorData = $this->processRestExceptionResult($e);
                self::assertEquals(self::FIXTURE_ASSET_ID, $errorData['parameters']['id']);
                self::assertEquals(Exception::HTTP_NOT_FOUND, $e->getCode());
            } elseif (constant('TESTS_WEB_API_ADAPTER') === self::ADAPTER_SOAP) {
                $this->assertInstanceOf('SoapFault', $e);
                $this->checkSoapFault(
                    $e,
                    $expectedMessage,
                    'env:Sender',
                    [1 => self::FIXTURE_ASSET_ID]
                );
            } else {
                throw $e;
            }
        }
    }

    /**
     * Test delete asset with incorrect asset id
     *
     * @return void
     */
    public function testDeleteWithIncorrectAssetId(): void
    {

        $notExistedAssetId = -1;
        $response = $this->deleteAsset($notExistedAssetId);
        if (constant('TESTS_WEB_API_ADAPTER') === self::ADAPTER_REST) {
            $this->assertSame([], $response);
        } else {
            $this->assertNull($response);
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
                'operation' => self::SERVICE_NAME . self::SERVICE_OPERATION_DELETE_BY_ID
            ],
        ];

        return (constant('TESTS_WEB_API_ADAPTER') === self::ADAPTER_SOAP)
            ? $this->_webApiCall($serviceInfo, ['id' => $assetId])
            : $this->_webApiCall($serviceInfo);
    }
}
