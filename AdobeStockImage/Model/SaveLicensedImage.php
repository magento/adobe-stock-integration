<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

declare(strict_types=1);

namespace Magento\AdobeStockImage\Model;

use Magento\AdobeStockAssetApi\Api\GetAssetByIdInterface;
use Magento\AdobeStockClientApi\Api\ClientInterface;
use Magento\AdobeStockImageApi\Api\SaveImageInterface;
use Magento\AdobeStockImageApi\Api\SaveLicensedImageInterface;
use Magento\Framework\Api\AttributeInterface;
use Magento\Framework\Api\AttributeInterfaceFactory;

/**
 * Backend controller for saving licensed image
 */
class SaveLicensedImage implements SaveLicensedImageInterface
{
    /**
     * @var GetAssetByIdInterface
     */
    private $getAssetById;

    /**
     * @var ClientInterface
     */
    private $client;

    /**
     * @var SaveImageInterface
     */
    private $saveImage;

    /**
     * @var AttributeInterfaceFactory
     */
    private $attributeFactory;

    /**
     * @param AttributeInterfaceFactory $attributeFactory
     * @param ClientInterface $client
     * @param SaveImageInterface $saveImage
     * @param GetAssetByIdInterface $getAssetById
     */
    public function __construct(
        AttributeInterfaceFactory $attributeFactory,
        ClientInterface $client,
        SaveImageInterface $saveImage,
        GetAssetByIdInterface $getAssetById
    ) {
        $this->attributeFactory = $attributeFactory;
        $this->client = $client;
        $this->saveImage = $saveImage;
        $this->getAssetById = $getAssetById;
    }

    /**
     * @inheritdoc
     */
    public function execute(int $mediaId, string $destinationPath = null): void
    {
        $imageUrl = $this->client->getImageDownloadUrl($mediaId);

        $document = $this->getAssetById->execute($mediaId);
        $pathAttribute = $document->getCustomAttribute('path');

        if (!empty($pathAttribute) && !empty($pathAttribute->getValue())) {
            $destinationPath = $pathAttribute->getValue();
        }

        $document->setCustomAttribute(
            'is_licensed',
            $this->attributeFactory->create(
                [
                    'data' => [
                        AttributeInterface::ATTRIBUTE_CODE => 'is_licensed',
                        AttributeInterface::VALUE => 1,
                    ]
                ]
            )
        );

        $this->saveImage->execute(
            $document,
            $imageUrl,
            $destinationPath
        );
    }
}
