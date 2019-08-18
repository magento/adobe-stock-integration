<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

declare(strict_types=1);

use Magento\AdobeStockAssetApi\Api\Data\KeywordInterface;
use Magento\AdobeStockAssetApi\Api\Data\KeywordInterfaceFactory;
use Magento\TestFramework\Helper\Bootstrap;

$objectManager = Bootstrap::getObjectManager();

/** @var KeywordInterfaceFactory $keywordFactory */
$keywordFactory = $objectManager->get(KeywordInterfaceFactory::class);

$keywordData = [
    [
        'id' => 1,
        'keyword' => 'alpha',
    ],
    [
        'id' => 2,
        'keyword' => 'betta',
    ],
    [
        'id' => 3,
        'keyword' => 'gama',
    ],
];

$keywords = [];
foreach ($keywordData as $keywordDatum) {
    /** @var KeywordInterface $keyword */
    $keyword = $keywordFactory->create();
    $keyword->setId($keywordDatum['id']);
    $keyword->setKeyword($keywordDatum['keyword']);
    $keywords[] = $keyword;
}

return $keywords;
