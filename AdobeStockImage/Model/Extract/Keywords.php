<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Magento\AdobeStockImage\Model\Extract;

use Magento\MediaGalleryApi\Api\Data\KeywordInterface;
use Magento\Framework\Api\Search\Document;
use Magento\MediaGalleryApi\Api\Data\KeywordInterfaceFactory;

/**
 * Keywords extractor
 */
class Keywords
{
    private const DOCUMENT_FIELD_KEYWORDS = 'keywords';
    private const DOCUMENT_FIELD_KEYWORD_NAME = 'name';

    private const KEYWORD_FIELD_KEYWORD_NAME = 'keyword';

    /**
     * @var KeywordInterfaceFactory
     */
    private $keywordFactory;

    /**
     * @param KeywordInterfaceFactory $keywordFactory
     */
    public function __construct(
        KeywordInterfaceFactory $keywordFactory
    ) {
        $this->keywordFactory = $keywordFactory;
    }

    /**
     * Convert search document to the asset object
     *
     * @param Document $document
     * @return KeywordInterface[]
     */
    public function convert(Document $document): array
    {
        $attribute = $document->getCustomAttribute(self::DOCUMENT_FIELD_KEYWORDS);

        if (!$attribute || !is_array($attribute->getValue())) {
            return [];
        }

        $keywords = [];
        foreach ($attribute->getValue() as $keywordData) {
            $keywords[] = $this->keywordFactory->create(
                [
                    'data' => [
                        self::KEYWORD_FIELD_KEYWORD_NAME => $keywordData[self::DOCUMENT_FIELD_KEYWORD_NAME]

                    ]
                ]
            );
        }

        return $keywords;
    }
}
