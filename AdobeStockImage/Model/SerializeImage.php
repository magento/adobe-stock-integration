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

/**
 * Class used to serialize a Document object
 */
class SerializeImage
{
    /**
     * @param Document $image
     * @return array
     * @throws SerializationException
     */
    public function execute(Document $image): array
    {
        $data = [];
        try {
            /** @var AttributeInterface $attribute */
            foreach ($image->getCustomAttributes() as $attribute) {
                if ($attribute->getAttributeCode() === 'thumbnail_240_url') {
                    $data['thumbnail_url'] = $attribute->getValue();
                    continue;
                }
                $data[$attribute->getAttributeCode()] = $attribute->getValue();
            }
            return $data;
        } catch (\Exception $exception) {
            throw new SerializationException(
                __(
                    'An error occurred during image serialization: %error',
                    ['error' => $exception->getMessage()]
                )
            );
        }
    }
}
