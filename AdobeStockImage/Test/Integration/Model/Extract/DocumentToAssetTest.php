<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Magento\AdobeStockImage\Test\Integration\Model\Extract;

use Magento\AdobeStockAssetApi\Api\Data\AssetInterface;
use Magento\AdobeStockAssetApi\Api\Data\CategoryInterface;
use Magento\AdobeStockAssetApi\Api\Data\CreatorInterface;
use Magento\AdobeStockImage\Model\Extract\AdobeStockAsset;
use Magento\Framework\Reflection\DataObjectProcessor;
use Magento\Framework\Api\AttributeInterface;
use Magento\Framework\Api\Search\Document;
use Magento\TestFramework\Helper\Bootstrap;
use PHPUnit\Framework\TestCase;

/**
 * Test converting a Document from the search result to Adobe Stock asset
 */
class DocumentToAssetTest extends TestCase
{
    /**
     * @var AdobeStockAsset
     */
    private $documentToAsset;

    /**
     * @var mixed
     */
    private $dataObjectProcessor;

    /**
     * Prepare test objects.
     */
    protected function setUp(): void
    {
        $this->documentToAsset = Bootstrap::getObjectManager()->get(
            AdobeStockAsset::class
        );
        $this->dataObjectProcessor = Bootstrap::getObjectManager()->get(
            DataObjectProcessor::class
        );
    }

    /**
     * @dataProvider documentProvider
     * @param array $data
     * @param array $additionalData
     */
    public function testConvert(
        array $data,
        array $additionalData
    ): void {
        $document = $this->getDocument($data);
        $asset = $this->documentToAsset->convert($document, $additionalData);
        $getCategory = $this->dataObjectProcessor
            ->buildOutputDataArray($asset->getCategory(), CategoryInterface::class);
        $this->assertEquals($data['creator_id'], $asset->getCreator()->getId());
        $this->assertEquals($data['creator_name'], $asset->getCreator()->getName());
        $this->assertEquals($data['category'], $getCategory);
        $this->assertEquals($data['id'], $asset->getId());
        $this->assertEquals($data['is_licensed'], $asset->getIsLicensed());
        $this->assertEquals($additionalData['media_gallery_id'], $asset->getMediaGalleryId());
        $this->assertInstanceOf(CategoryInterface::class, $asset['category']);
        $this->assertInstanceOf(CreatorInterface::class, $asset['creator']);
        $this->assertInstanceOf(AssetInterface::class, $asset);
    }

    /**
     * @return array
     */
    public function documentProvider(): array
    {
        return [
            'case1' => [
                'data' => [
                    'id' => 1,
                    'category' => [
                        'id' => 2,
                        'name' => 'The Category'
                    ],
                    'creator_name' => 'Creator',
                    'creator_id' => 3,
                    'is_licensed' => 1
                ],
                'additionaData' => [
                    'media_gallery_id' => 5
                ]
            ]
        ];
    }

    /**
     * @param array $attributes
     * @return Document
     */
    private function getDocument(array $attributes): Document
    {
        $document = Bootstrap::getObjectManager()->get(Document::class);
        $customAttributes = [];

        foreach ($attributes as $key => $value) {
            $attribute = Bootstrap::getObjectManager()->create(AttributeInterface::class);
            $attribute->setAttributeCode($key)->setValue($value);
            $customAttributes[] = $attribute;
        }
        $document->setCustomAttributes($customAttributes);

        return $document;
    }
}
