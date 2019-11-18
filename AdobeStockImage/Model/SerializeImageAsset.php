<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

declare(strict_types=1);

namespace Magento\AdobeStockImage\Model;

use Magento\Framework\Api\AttributeInterface;
use Magento\Framework\Api\Search\Document;
use Magento\Framework\Exception\SerializationException;
use Psr\Log\LoggerInterface;

class SerializeImageAsset
{
    /**
     * @var LoggerInterface
     */
    private $logger;

    /**
     * SerializeImageAsset constructor.
     *
     * @param LoggerInterface $logger
     */
    public function __construct(LoggerInterface $logger)
    {
        $this->logger = $logger;
    }

    /**
     * Serialize image asset data.
     *
     * @param Document[] $images
     * @return array
     * @throws SerializationException
     */
    public function execute(array $images): array
    {
        $data = [];
        try {
            /** @var Document $image */
            foreach ($images as $image) {
                $itemData = [];
                /** @var AttributeInterface $attribute */
                foreach ($image->getCustomAttributes() as $attribute) {
                    if ($attribute->getAttributeCode() === 'thumbnail_240_url') {
                        $itemData['thumbnail_url'] = $attribute->getValue();
                        continue;
                    }
                    $itemData[$attribute->getAttributeCode()] = $attribute->getValue();
                }
                $data[] = $itemData;
            }
            return $data;
        } catch (\Exception $exception) {
            $this->logger->critical($exception);
            throw new SerializationException(
                __(
                    'An error occurred during asset data serialization: %error',
                    ['error' => $exception->getMessage()]
                )
            );
        }
    }
}
