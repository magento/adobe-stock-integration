<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Magento\AdobeStockAsset\Model;

use Magento\AdobeStockAsset\Model\Keyword\Command\DeleteByIdInterface;
use Magento\AdobeStockAsset\Model\Keyword\Command\GetInterface;
use Magento\AdobeStockAsset\Model\Keyword\Command\GetListInterface;
use Magento\AdobeStockAsset\Model\Keyword\Command\SaveInterface;
use Magento\AdobeStockAssetApi\Api\Data\KeywordInterface;
use Magento\AdobeStockAssetApi\Api\Data\KeywordSearchResultsInterface;
use Magento\AdobeStockAssetApi\Api\KeywordRepositoryInterface;
use Magento\Framework\Api\SearchCriteriaInterface;

/**
 * @inheritdoc
 */
class KeywordRepository implements KeywordRepositoryInterface
{
    /**
     * @var SaveInterface
     */
    private $commandSave;

    /**
     * @var GetInterface
     */
    private $commandGet;

    /**
     * @var DeleteByIdInterface
     */
    private $commandDeleteById;

    /**
     * @var GetListInterface
     */
    private $commandGetList;

    /**
     * @param SaveInterface $commandSave
     * @param GetInterface $commandGet
     * @param DeleteByIdInterface $commandDeleteById
     * @param GetListInterface $commandGetList
     */
    public function __construct(
        SaveInterface $commandSave,
        GetInterface $commandGet,
        DeleteByIdInterface $commandDeleteById,
        GetListInterface $commandGetList
    ) {
        $this->commandSave = $commandSave;
        $this->commandGet = $commandGet;
        $this->commandDeleteById = $commandDeleteById;
        $this->commandGetList = $commandGetList;
    }

    /**
     * @inheritdoc
     */
    public function save(KeywordInterface $keyword): int
    {
        return $this->commandSave->execute($keyword);
    }

    /**
     * @inheritdoc
     */
    public function get(int $keywordId): KeywordInterface
    {
        return $this->commandGet->execute($keywordId);
    }

    /**
     * @inheritdoc
     */
    public function deleteById(int $keywordId): void
    {
        $this->commandDeleteById->execute($keywordId);
    }

    /**
     * @inheritdoc
     */
    public function getList(SearchCriteriaInterface $searchCriteria = null): KeywordSearchResultsInterface
    {
        return $this->commandGetList->execute($searchCriteria);
    }
}
