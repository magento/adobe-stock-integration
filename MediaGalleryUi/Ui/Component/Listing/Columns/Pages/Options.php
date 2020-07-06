<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Magento\MediaGalleryUi\Ui\Component\Listing\Columns\Pages;

use Magento\Cms\Api\PageRepositoryInterface;
use Magento\Framework\Api\SearchCriteriaBuilder;
use Magento\Framework\Api\FilterBuilder;
use Magento\Framework\Data\OptionSourceInterface;

/**
 * Image source filter options
 */
class Options implements OptionSourceInterface
{
    private $PageRepository;

    private $SearchCriteriaBuilder;

    private $FilterBuilder;

    /**
     * @param PageRepositoryInterface $pageRepository
     * @param SearchCriteriaBuilder $searchCriteriaBuilder
     * @param FilterBuilder $filterBuilder
     */
    public function __construct(
        PageRepositoryInterface $pageRepository,
        SearchCriteriaBuilder $searchCriteriaBuilder,
        FilterBuilder $filterBuilder
    ) {
        $this->PageRepository = $pageRepository;
        $this->SearchCriteriaBuilder = $searchCriteriaBuilder;
        $this->FilterBuilder = $filterBuilder;
    }

    /**
     * @inheritdoc
     */
    public function toOptionArray(): array
    {
        $searchCriteria = $this->SearchCriteriaBuilder->create();
        $pages = [];
            $result = $this->PageRepository->getList($searchCriteria);
            /** @var \Magento\Cms\Api\Data\PageInterface $page */
            foreach ($result->getItems() as $page) {
                $pages[] = [
                    'value' => $page->getId(),
                    'label' => $page->getTitle()
                ];
            }
        return $pages;
    }
}
