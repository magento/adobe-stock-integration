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
use Magento\Framework\Exception\CouldNotDeleteException;
use Magento\Framework\Exception\NoSuchEntityException;
use Psr\Log\LoggerInterface;

/**
 * @inheritdoc
 */
class DeleteById implements DeleteByIdInterface
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
     * @var LoggerInterface
     */
    private $logger;

    /**
     * DeleteById constructor.
     *
     * @param KeywordResourceModel    $keywordResource
     * @param KeywordInterfaceFactory $keywordFactory
     * @param LoggerInterface         $logger
     */
    public function __construct(
        KeywordResourceModel $keywordResource,
        KeywordInterfaceFactory $keywordFactory,
        LoggerInterface $logger
    ) {
        $this->keywordResource = $keywordResource;
        $this->keywordFactory = $keywordFactory;
        $this->logger = $logger;
    }

    /**
     * @inheritdoc
     */
    public function execute(int $keywordId)
    {
        /** @var KeywordInterface $keyword */
        $keyword = $this->keywordFactory->create();
        $this->keywordResource->load($keyword, $keywordId, KeywordInterface::ID);

        if (null === $keyword->getId()) {
            throw new NoSuchEntityException(
                __(
                    'There is no keyword with "%fieldValue" for "%fieldName". Verify and try again.',
                    [
                        'fieldName' => KeywordInterface::ID,
                        'fieldValue' => $keywordId,
                    ]
                )
            );
        }

        try {
            $this->keywordResource->delete($keyword);
        } catch (\Exception $exception) {
            $this->logger->error($exception->getMessage());
            throw new CouldNotDeleteException(__('Could not delete a Keyword'), $exception);
        }
    }
}
