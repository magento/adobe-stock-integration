<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Magento\AdobeStockImage\Model;

use Magento\Framework\Api\AttributeInterface;
use Magento\Framework\Api\Search\Document;

/**
 * Class used to serialize a Document object
 */
class SerializeImage
{
    /**
     * Serializes a Document object
     *
     * @param Document $image
     * @return array
     */
    public function execute(Document $image): array
    {
        $data = [];
        /** @var AttributeInterface $attribute */
        foreach ($image->getCustomAttributes() as $attribute) {
            if ($attribute->getAttributeCode() === 'thumbnail_240_url') {
                $data['thumbnail_url'] = $attribute->getValue();
                continue;
            }
            $data[$attribute->getAttributeCode()] = $attribute->getValue();
        }
        return $data;
    }
}
