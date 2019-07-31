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
    const SERVICE_NAME = 'adobeStockAssetRepositoryV1';
    const SERVICE_VERSION = 'V1';
    const RESOURCE_PATH = '/V1/adobestock/asset';

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
     * @param array $data
     * @dataProvider assetDataProvider
     */
    public function testSave(array $data)
    {
        $this->saveAsset($data);
        /** @var Asset $asset */
        $asset = $this->getSavedAsset($data[AssetInterface::ADOBE_ID]);

        $this->assertArraySubset($data, $asset->getData());
    }

    /**
     * @return array
     */
    public function assetDataProvider()
    {
        return [
            [
                [
                    AssetInterface::ADOBE_ID => (string) random_int(9999, 99999),
                    AssetInterface::PATH => uniqid() . '/file-path.png',
                    AssetInterface::WIDTH => '1000',
                    AssetInterface::HEIGHT => '800',
                    AssetInterface::PREVIEW_WIDTH => '500',
                    AssetInterface::PREVIEW_HEIGHT => '400',
                    AssetInterface::PREVIEW_URL => uniqid('preview-url'),
                    AssetInterface::DETAILS_URL => uniqid('details-url'),
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
                'operation' => self::SERVICE_NAME . 'save',
            ],
        ];

        $requestData = ['item' => $data];

        return $this->_webApiCall($serviceInfo, $requestData, null);
    }

    /**
     * @param string $adobeId
     * @return AssetInterface
     */
    private function getSavedAsset(string $adobeId): AssetInterface
    {
        /** @var Collection $collection */
        $collection = $this->assetCollectionFactory->create();
        $collection->addFieldToFilter(AssetInterface::ADOBE_ID, $adobeId);
        /** @var AssetInterface $asset */
        $asset = $collection->getLastItem();

        return $asset;
    }
}
