<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

declare(strict_types=1);

namespace Magento\AdobeStockAsset\Model;

use Magento\AdobeStockAssetApi\Api\Data\AssetInterface;
use Magento\AdobeStockAssetApi\Api\Data\AssetInterfaceFactory;
use Magento\AdobeStockAssetApi\Api\Data\CategoryInterface;
use Magento\AdobeStockAssetApi\Api\Data\CategoryInterfaceFactory;
use Magento\AdobeStockAssetApi\Api\Data\CreatorInterface;
use Magento\AdobeStockAssetApi\Api\Data\CreatorInterfaceFactory;
use Magento\Framework\Api\Search\Document;
use Magento\Framework\Exception\IntegrationException;
use Magento\Payment\Gateway\Http\ConverterException;

/**
 * Class DocumentToAsset
 */
class DocumentToAsset
{
    /**
     * @var array
     */
    private $mapping;

    /**
     * DocumentToAsset constructor.
     * @param array $mapping
     */
    public function __construct(array $mapping)
    {
        $this->mapping = $mapping;
    }

    /**
     * Convert search document to the asset object
     *
     * @param Document $document
     * @return AssetInterface
     * @throws ConverterException
     */
    public function convert(Document $document): AssetInterface
    {
        try {
            $attributes = $document->getCustomAttributes();
            $data = [];
            foreach ($attributes as $attribute) {
                $data[$attribute->getAttributeCode()] = $attribute->getValue();
            }
            $asset = $this->createEntity(
                $data,
                $this->mapping['factory'],
                $this->mapping['fields'],
                $this->mapping['children']
            );
            foreach ($data as $key => $value) {
                $asset->setData($key, $value);
            }
            return $asset;
        } catch (\Exception $exception) {
            $message = __('Convert search document to asset failed: %1', $exception->getMessage());
            throw new ConverterException($message, $exception);
        }
    }

    /**
     * Create asset data entity in recursive loop.
     */
    private function createEntity(&$data, $factory, $fields = [], $children = [])
    {
        $data = (array) $data;
        $entity = $factory->create();

        foreach ($children as $childName => $childMapping) {
            $entity->setData(
                $childName,
                $this->createEntity(
                    $data,
                    $childMapping['factory'],
                    $childMapping['fields'] ?? [],
                    $childMapping['children'] ?? []
                )
            );
            unset($data[$childName]);
        }
        foreach ($fields as $documentField => $assetField) {
            if (is_array($assetField) && is_array($data[$documentField])) {
                $items = [];
                foreach ($data[$documentField] as $itemData) {
                    $items[] = $this->createEntity($itemData, $factory, $assetField, $children);
                }
                return $items;
            } else {
                $entity->setData($assetField, $data[$documentField]);
            }
            unset($data[$documentField]);
        }

        return $entity;
    }
}
