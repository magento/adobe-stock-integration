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
    /**
     * Service name
     */
    const SERVICE_NAME = 'adobeStockAssetApiAssetRepositoryV1';

    /**
     * Service version
     */
    const SERVICE_VERSION = 'V1';

    /**
     * Resource path
     */
    const RESOURCE_PATH = '/V1/adobestock/asset';

    /**
     * Service operation
     */
    const SERVICE_OPERATION = 'Save';

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
        $uniqueData = [
            AssetInterface::ADOBE_ID => $data[AssetInterface::ADOBE_ID],
            AssetInterface::PATH => $data[AssetInterface::PATH],
            AssetInterface::PREVIEW_URL => $data[AssetInterface::PREVIEW_URL],
            AssetInterface::DETAILS_URL => $data[AssetInterface::DETAILS_URL]
        ];

        $this->assertArraySubset($uniqueData, $asset->getData());
    }

    /**
     * @return array
     */
    public function assetDataProvider()
    {
        if (TESTS_WEB_API_ADAPTER === self::ADAPTER_REST) {
            return [
                [
                    [
                        AssetInterface::ADOBE_ID => (string) mt_rand(9999, 99999),
                        AssetInterface::PATH => uniqid() . '/file-path.png',
                        AssetInterface::WIDTH => '1000',
                        AssetInterface::HEIGHT => '800',
                        AssetInterface::PREVIEW_WIDTH => '500',
                        AssetInterface::PREVIEW_HEIGHT => '400',
                        AssetInterface::URL => uniqid('url'),
                        AssetInterface::PREVIEW_URL => uniqid('preview-url'),
                        AssetInterface::DETAILS_URL => uniqid('details-url'),
                    ]
                ]
            ];
        }

        return [
            [
                [
                    AssetInterface::ADOBE_ID => (string) mt_rand(9999, 99999),
                    AssetInterface::PATH => uniqid() . '/file-path.png',
                    AssetInterface::WIDTH => '1000',
                    AssetInterface::HEIGHT => '800',
                    AssetInterface::PREVIEW_WIDTH => '500',
                    AssetInterface::PREVIEW_HEIGHT => '400',
                    AssetInterface::MEDIA_TYPE_ID => null,
                    AssetInterface::KEYWORDS => [],
                    AssetInterface::PREMIUM_LEVEL_ID => null,
                    AssetInterface::STOCK_ID => 1,
                    AssetInterface::TITLE => 'Title',
                    AssetInterface::URL => uniqid('preview-url'),
                    AssetInterface::COUNTRY_NAME => '',
                    AssetInterface::VECTOR_TYPE => '',
                    AssetInterface::CONTENT_TYPE => '',
                    AssetInterface::CREATION_DATE => '',
                    AssetInterface::CREATED_AT => '',
                    AssetInterface::UPDATED_AT => '',
                    AssetInterface::PREVIEW_URL => uniqid('preview-url'),
                    AssetInterface::DETAILS_URL => uniqid('details-url'),
                    AssetInterface::IS_LICENSED => 1,
                    'licensed' => 1,
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
        $collection->addFieldToFilter(AssetInterface::ADOBE_ID, $adobeId);
        /** @var AssetInterface $asset */
        $asset = $collection->getLastItem();

        return $asset;
    }
}
