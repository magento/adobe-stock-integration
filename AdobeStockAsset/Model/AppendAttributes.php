<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Magento\AdobeStockAsset\Model;

use Magento\MediaGalleryApi\Model\Asset\Command\GetByIdInterface;
use Magento\AdobeStockAsset\Model\ResourceModel\Asset\LoadByIds;
use Magento\Framework\Api\AttributeValueFactory;
use Magento\Framework\Api\Search\Document;
use Magento\Framework\Api\Search\SearchResultInterface;
use Magento\Framework\App\ResourceConnection;

/**
 * Class is used for adding an additional assets attributes such as is_downloaded or path to the search results
 */
class AppendAttributes
{
    private const ATTRIBUTE_CODE_IS_DOWNLOADED = 'is_downloaded';
    private const ATTRIBUTE_CODE_IS_LICENSED_LOCALLY = 'is_licensed_locally';
    private const ATTRIBUTE_CODE_PATH = 'path';

    /**
     * @var AttributeValueFactory
     */
    private $attributeValueFactory;

    /**
     * @var ResourceConnection
     */
    private $resourceConnection;

    /**
     * @var LoadByIds
     */
    private $loadByIds;

    /**
     * @var GetByIdInterface
     */
    private $getMediaGalleryAssetById;

    /**
     * @param ResourceConnection $resourceConnection
     * @param AttributeValueFactory $attributeValueFactory
     * @param LoadByIds $loadByIds
     * @param GetByIdInterface $getMediaGalleryAssetById
     */
    public function __construct(
        ResourceConnection $resourceConnection,
        AttributeValueFactory $attributeValueFactory,
        LoadByIds $loadByIds,
        GetByIdInterface $getMediaGalleryAssetById
    ) {
        $this->resourceConnection = $resourceConnection;
        $this->attributeValueFactory = $attributeValueFactory;
        $this->loadByIds = $loadByIds;
        $this->getMediaGalleryAssetById = $getMediaGalleryAssetById;
    }

    /**
     * Add additional asset attributes
     *
     * @param SearchResultInterface $searchResult
     * @return SearchResultInterface
     */
    public function execute(SearchResultInterface $searchResult): SearchResultInterface
    {
        $items = $searchResult->getItems();

        if (empty($items)) {
            return $searchResult;
        }

        $ids = array_map(
            function ($item) {
                return $item->getId();
            },
            $items
        );

        $assets = $this->loadByIds->execute($ids);

        foreach ($items as $key => $item) {
            if (!isset($assets[$item->getId()])) {
                $this->addAttributes(
                    $item,
                    [
                        self::ATTRIBUTE_CODE_IS_DOWNLOADED => 0,
                        self::ATTRIBUTE_CODE_PATH => '',
                        self::ATTRIBUTE_CODE_IS_LICENSED_LOCALLY => 0
                    ]
                );
                continue;
            }

            $path = $this->getMediaGalleryAssetById->execute(
                $assets[$item->getId()]->getMediaGalleryId()
            )->getPath();

            $this->addAttributes(
                $item,
                [
                    self::ATTRIBUTE_CODE_IS_DOWNLOADED => 1,
                    self::ATTRIBUTE_CODE_PATH => $path,
                    self::ATTRIBUTE_CODE_IS_LICENSED_LOCALLY => $assets[$item->getId()]->getIsLicensed() ?? 0
                ]
            );
        }

        return $searchResult;
    }

    /**
     * Add attributes to document
     *
     * @param Document $document
     * @param array $attributes [code => value]
     * @return Document
     */
    private function addAttributes(Document $document, array $attributes): Document
    {
        $customAttributes = $document->getCustomAttributes();

        foreach ($attributes as $code => $value) {
            $attribute = $this->attributeValueFactory->create();
            $attribute->setAttributeCode($code);
            $attribute->setValue($value);
            $customAttributes[$code] = $attribute;
        }

        $document->setCustomAttributes($customAttributes);

        return $document;
    }
}
