<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Magento\AdobeStockAsset\Model\Keyword\Command;

use Magento\AdobeStockAsset\Model\ResourceModel\Keyword as KeywordResourceModel;
use Magento\AdobeStockAssetApi\Api\Data\KeywordInterface;
use Magento\AdobeStockAssetApi\Api\Data\KeywordInterfaceFactory;
use Magento\Framework\Exception\NoSuchEntityException;

/**
 * @inheritdoc
 */
class Get implements GetInterface
{
    /**
     * @var KeywordResourceModel
     */
    private $keywordResource;

    /**
     * @var KeywordInterfaceFactory
     */
    private $keywordFactory;

    /**
     * Get constructor.
     *
     * @param KeywordResourceModel    $keywordResource
     * @param KeywordInterfaceFactory $keywordFactory
     */
    public function __construct(
        KeywordResourceModel $keywordResource,
        KeywordInterfaceFactory $keywordFactory
    ) {
        $this->keywordResource = $keywordResource;
        $this->keywordFactory = $keywordFactory;
    }

    /**
     * @inheritdoc
     */
    public function execute(int $keywordId): KeywordInterface
    {
        /** @var KeywordInterface $keyword */
        $keyword = $this->keywordFactory->create();
        $this->keywordResource->load($keyword, $keywordId, KeywordInterface::ID);

        if (null === $keyword->getId()) {
            throw new NoSuchEntityException(__('Keyword with id "%value" does not exist.', ['value' => $keywordId,]));
        }

        return $keyword;
    }
}
