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
     * Prepare test objects.
     */
    protected function setUp(): void
    {
        $this->documentToAsset = Bootstrap::getObjectManager()->get(
            AdobeStockAsset::class
        );
    }

    /**
     * @dataProvider documentProvider
     * @param array $assetData
     * @param array $categoryData
     * @param array $creatorData
     * @param Document $document
     * @param array $additionalData
     */
    public function testConvert(
        array $assetData,
        array $categoryData,
        array $creatorData,
        Document $document,
        array $additionalData
    ): void {
        $this->documentToAsset = Bootstrap::getObjectManager()->get(
            AdobeStockAsset::class
        );
        $documentToAsset = $this->documentToAsset->convert($document, $additionalData);
        $this->assertEquals($creatorData, $documentToAsset->getCreator()->getData());
        $this->assertEquals($categoryData, $documentToAsset->getCategory()->getData());
        $this->assertEquals($assetData['id'], $documentToAsset->getId());
        $this->assertEquals($assetData['is_licensed'], $documentToAsset->getIsLicensed());
        $this->assertEquals($assetData['media_gallery_id'], $documentToAsset->getMediaGalleryId());
        $this->assertInstanceOf(CategoryInterface::class, $documentToAsset['category']);
        $this->assertInstanceOf(CreatorInterface::class, $documentToAsset['creator']);
        $this->assertInstanceOf(AssetInterface::class, $documentToAsset);
    }

    /**
     * @return array
     */
    public function documentProvider(): array
    {
        return [
            'case1' => [
                'assetData' => [
                    'id' => 1,
                    'is_licensed' => 1,
                    'media_gallery_id' => 5
                ],
                'categoryData' => [
                    'id' => 2,
                    'name' => 'The Category'
                ],
                'creatorData' => [
                    'name' => 'Creator',
                    'id' => 3,
                ],
                'document' => $this->getDocument(
                    [
                        'id' => 1,
                        'category' => [
                            'id' => 2,
                            'name' => 'The Category'
                        ],
                        'creator_name' => 'Creator',
                        'creator_id' => 3,
                        'is_licensed' => 1
                    ]
                ),
                [
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
