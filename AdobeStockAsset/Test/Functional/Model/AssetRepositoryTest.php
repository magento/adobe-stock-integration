<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Magento\AdobeStockAsset\Model;

use Magento\AdobeStockAssetApi\Api\Data\AssetInterface;
use Magento\Framework\Webapi\Rest\Request;
use Magento\TestFramework\TestCase\WebapiAbstract;

class AssetRepositoryTest extends WebapiAbstract
{
    const SERVICE_NAME = 'adobeStockAssetRepositoryV1';
    const SERVICE_VERSION = 'V1';
    const RESOURCE_PATH = '/V1/adobestock/asset';

    /**
     * @magentoApiDataFixture customFixtureProvider
     */
    public function testDelete()
    {
        $response = $this->deleteAsset('1');
        $this->assertSame([], $response);
    }

    /**
     * @param $asset
     * @dataProvider assetCreationProvider
     */
    public function testSave($asset)
    {
        $response = $this->saveAsset($asset);
        $this->assertArrayHasKey(AssetInterface::ID, $response);
        $this->deleteAsset($asset[AssetInterface::ID]);
    }

    /**
     * Delete Adobe Stock Asset
     *
     * @param string $assetId
     * @return array|bool|float|int|string
     */
    private function deleteAsset(string $assetId)
    {
        $serviceInfo = [
            'rest' => [
                'resourcePath' => self::RESOURCE_PATH . DIRECTORY_SEPARATOR . $assetId,
                'httpMethod'   => Request::HTTP_METHOD_DELETE,
            ],
            'soap' => [
                'service'        => self::SERVICE_NAME,
                'serviceVersion' => self::SERVICE_VERSION,
                'opertion'       => self::SERVICE_NAME . 'DeleteById',
            ],
        ];

        return (TESTS_WEB_API_ADAPTER === self::ADAPTER_SOAP) ?
            $this->_webApiCall($serviceInfo, [[AssetInterface::ID => $assetId]]) : $this->_webApiCall($serviceInfo);
    }

    public static function customFixtureProvider()
    {
        require __DIR__ . '/../../Integration/_files/asset.php';
    }

    /**
     * @return array
     */
    public function assetCreationProvider()
    {
        return [[AssetInterface::ID => 1]];
    }

    /**
     * @param array $assetId
     * @return array
     */
    private function saveAsset($assetId)
    {
        $serviceInfo = [
            'rest' => [
                'resourcePath' => self::RESOURCE_PATH,
                'httpMethod'   => Request::HTTP_METHOD_POST,

            ],
            'soap' => [
                'service'        => self::SERVICE_NAME,
                'serviceVersion' => self::SERVICE_VERSION,
                'operation'      => self::SERVICE_NAME . 'save',
            ],
        ];

        $requestData = ['asset' => [AssetInterface::ID => $assetId]];

        return $this->_webApiCall($serviceInfo, $requestData, null);
    }
}
