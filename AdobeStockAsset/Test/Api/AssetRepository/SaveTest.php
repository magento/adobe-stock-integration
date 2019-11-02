<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Magento\AdobeStockAsset\Test\Api\AssetRepository;

use Magento\AdobeStockAsset\Model\Asset;
use Magento\AdobeStockAsset\Model\ResourceModel\Asset\CollectionFactory;
use Magento\AdobeStockAsset\Model\ResourceModel\Asset\Collection;
use Magento\AdobeStockAssetApi\Api\Data\AssetInterface;
use Magento\Framework\ObjectManagerInterface;
use Magento\Framework\Webapi\Rest\Request;
use Magento\TestFramework\Helper\Bootstrap;
use Magento\TestFramework\TestCase\WebapiAbstract;

/**
 * Testing save asset web api
 */
class SaveTest extends WebapiAbstract
{
    private const SERVICE_NAME = 'adobeStockAssetApiAssetRepositoryV1';

    private const SERVICE_VERSION = 'V1';

    private const RESOURCE_PATH = '/V1/adobestock/asset';

    private const SERVICE_OPERATION = 'Save';

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
    public function setUp(): void
    {
        $this->objectManager = Bootstrap::getObjectManager();
        $this->assetCollectionFactory = $this->objectManager->get(CollectionFactory::class);
    }

    /**
     * @param array $data
     * @dataProvider assetDataProvider
     */
    public function testSave(array $data): void
    {
        $this->saveAsset($data);
        /** @var Asset $asset */
        $asset = $this->getSavedAsset($data['id']);
        $uniqueData = [
            'id' => $data['id'],
            'media_gallery_id' => $data['media_gallery_id'],
        ];

        $this->assertArraySubset($uniqueData, $asset->getData());
    }

    /**
     * @return array
     * @throws \Exception
     */
    public function assetDataProvider(): array
    {
        return [
            [
                [
                   'id' => (string) random_int(9999, 99999),
                    'creation_date' => '',
                    'is_licensed' => 1,
                ]
            ]
        ];
    }

    /**
     * @param array $data
     * @return array
     */
    private function saveAsset(array $data)
    {
        $serviceInfo = [
            'rest' => [
                'resourcePath' => self::RESOURCE_PATH,
                'httpMethod' => Request::HTTP_METHOD_POST,

            ],
            'soap' => [
                'service' => self::SERVICE_NAME,
                'serviceVersion' => self::SERVICE_VERSION,
                'operation' => self::SERVICE_NAME . self::SERVICE_OPERATION,
            ],
        ];
        $requestData = ['asset' => $data];

        return $this->_webApiCall($serviceInfo, $requestData);
    }

    /**
     * @param string $adobeId
     * @return AssetInterface
     */
    private function getSavedAsset(string $adobeId): AssetInterface
    {
        /** @var Collection $collection */
        $collection = $this->assetCollectionFactory->create();
        $collection->addFieldToFilter('id', $adobeId);
        /** @var AssetInterface $asset */
        $asset = $collection->getLastItem();

        return $asset;
    }
}
