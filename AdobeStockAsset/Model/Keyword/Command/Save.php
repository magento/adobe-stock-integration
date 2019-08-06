<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Magento\AdobeStockAsset\Model\Keyword\Command;

use Magento\AdobeStockAsset\Model\ResourceModel\Keyword as KeywordResourceModel;
use Magento\AdobeStockAssetApi\Api\Data\KeywordInterface;
use Magento\Framework\Exception\CouldNotSaveException;
use Psr\Log\LoggerInterface;

/**
 * @inheritdoc
 */
class Save implements SaveInterface
{
    /**
     * @var KeywordResourceModel
     */
    private $keywordResource;

    /**
     * @var LoggerInterface
     */
    private $logger;

    public function __construct(
        KeywordResourceModel $keywordResource,
        LoggerInterface $logger
    ) {
        $this->keywordResource = $keywordResource;
        $this->logger = $logger;
    }

    /**
     * @inheritdoc
     */
    public function execute(KeywordInterface $keyword): int
    {
        try {
            $this->keywordResource->save($keyword);
            return (int) $keyword->getId();
        } catch (\Exception $exception) {
            $this->logger->error($exception->getMessage());
            throw new CouldNotSaveException(__('Could not save a keyword'), $exception);
        }
    }
}
