<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

declare(strict_types=1);

namespace Magento\AdobeStockClient\Model;

use AdobeStock\Api\Models\StockFile;
use Exception;
use Magento\Framework\Api\AttributeValue;
use Magento\Framework\Api\Search\DocumentFactory;
use Magento\Framework\Api\Search\Document;
use Magento\Framework\Exception\IntegrationException;
use Magento\Framework\Api\AttributeValueFactory;
use Magento\Framework\Phrase;
use Psr\Log\LoggerInterface;

/**
 * Used for converting the stock document to the search document object
 */
class StockFileToDocument
{
    /**
     * @var DocumentFactory
     */
    private $documentFactory;

    /**
     * @var AttributeValueFactory
     */
    private $attributeValueFactory;

    /**
     * @var LoggerInterface
     */
    private $logger;

    /**
     * @param DocumentFactory $documentFactory
     * @param AttributeValueFactory $attributeValueFactory
     * @param LoggerInterface $logger
     */
    public function __construct(
        DocumentFactory $documentFactory,
        AttributeValueFactory $attributeValueFactory,
        LoggerInterface $logger
    ) {
        $this->documentFactory = $documentFactory;
        $this->attributeValueFactory = $attributeValueFactory;
        $this->logger = $logger;
    }

    /**
     * Convert a stock file object to a document object
     *
     * @param StockFile $file
     * @return Document
     * @throws IntegrationException
     */
    public function convert(StockFile $file): Document
    {
        $itemData = (array) $file;
        $itemId = $itemData['id'];

        $category = (array) $itemData['category'];

        $itemData['category'] = $category;
        $itemData['category_id'] = $category['id'];
        $itemData['category_name'] = $category['name'];

        $attributes = $this->createAttributes('id', $this->toArray($itemData));

        $item = $this->documentFactory->create();
        $item->setId($itemId);
        $item->setCustomAttributes($attributes);

        return $item;
    }

    /**
     * Convert data to an associate array
     *
     * @param object|array $data
     * @return mixed
     */
    private function toArray($data)
    {
        if (is_object($data) || is_array($data)) {
            $array = [];
            foreach ((array)$data as $key => $item) {
                $array[$key] = $this->toArray($item);
            }
            return $array;
        }

        return $data;
    }

    /**
     * Create custom attributes for columns returned by search
     *
     * @param string $idFieldName
     * @param array $itemData
     * @return AttributeValue[]
     * @throws IntegrationException
     */
    private function createAttributes(string $idFieldName, array $itemData): array
    {
        try {
            $attributes = [];

            $idFieldNameAttribute = $this->attributeValueFactory->create();
            $idFieldNameAttribute->setAttributeCode('id_field_name');
            $idFieldNameAttribute->setValue($idFieldName);
            $attributes['id_field_name'] = $idFieldNameAttribute;

            foreach ($itemData as $key => $value) {
                if ($value === null) {
                    continue;
                }
                $attribute = $this->attributeValueFactory->create();
                $attribute->setAttributeCode($key);
                if (is_bool($value)) {
                    // for proper work of form and grid (for example for Yes/No properties)
                    $value = (string)(int)$value;
                }
                $attribute->setValue($value);
                $attributes[$key] = $attribute;
            }
            return $attributes;
        } catch (Exception $exception) {
            $message = __(
                'Create attributes process failed: %error_message',
                ['error_message' => $exception->getMessage()]
            );
            $this->processException($message, $exception);
        }
    }

    /**
     * Handle SDK Exception and throw Magento exception instead
     *
     * @param Phrase $message
     * @param Exception $exception
     * @throws IntegrationException
     */
    private function processException(Phrase $message, Exception $exception): void
    {
        $this->logger->critical($exception);
        throw new IntegrationException($message, $exception, $exception->getCode());
    }
}
