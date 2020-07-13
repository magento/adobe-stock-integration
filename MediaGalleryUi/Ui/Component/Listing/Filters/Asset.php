<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

declare(strict_types=1);

namespace Magento\MediaGalleryUi\Ui\Component\Listing\Filters;

use Magento\Ui\Component\Filters\Type\Select;
use Magento\MediaGalleryApi\Api\GetAssetsByPathsInterface;
use Magento\Framework\View\Element\UiComponentFactory;
use Magento\Framework\View\Element\UiComponent\ContextInterface;
use Magento\Framework\Api\FilterBuilder;
use Magento\Ui\Component\Filters\FilterModifier;
use Magento\MediaContentApi\Api\GetContentByAssetIdsInterface;
use Magento\Framework\Data\OptionSourceInterface;

/**
 * Asset  filter
 */
class Asset extends Select
{
    private const CTAEGORY_ENTITY_TYPE = 'catalog_category';

    /**
     * @var GetContentByAssetIdsInterface
     */
    private $getContentIdentities;

    /**
     * @param ContextInterface $context
     * @param UiComponentFactory $uiComponentFactory
     * @param FilterBuilder $filterBuilder
     * @param FilterModifier $filterModifier
     * @param OptionSourceInterface $optionsProvider
     * @param GetContentByAssetIdsInterface $getContentIdentities
     * @param array $components
     * @param array $data
     */
    public function __construct(
        ContextInterface $context,
        UiComponentFactory $uiComponentFactory,
        FilterBuilder $filterBuilder,
        FilterModifier $filterModifier,
        OptionSourceInterface $optionsProvider = null,
        GetContentByAssetIdsInterface $getContentIdentities,
        array $components = [],
        array $data = []
    ) {
        $this->uiComponentFactory = $uiComponentFactory;
        $this->filterBuilder = $filterBuilder;
        parent::__construct(
            $context,
            $uiComponentFactory,
            $filterBuilder,
            $filterModifier,
            $optionsProvider,
            $components,
            $data
        );
        $this->getContentIdentities = $getContentIdentities;
    }
    
    /**
     * Apply filter
     *
     * @return void
     */
    protected function applyFilter()
    {
        if (isset($this->filterData[$this->getName()])) {
            $ids = $this->filterData[$this->getName()];
            $filter = $this->filterBuilder->setConditionType('in')
                    ->setField($this->_data['config']['identityColumn'])
                    ->setValue($this->getCategoryIdsByAsset($ids))
                    ->create();

            $this->getContext()->getDataProvider()->addFilter($filter);
        }
    }

    /**
     * Return category ids by assets ids.
     *
     * @param string $ids
     */
    private function getCategoryIdsByAsset(array $ids): string
    {
        if (!empty($ids)) {
            $categoryIds = [];
            $data = $this->getContentIdentities->execute($ids);
            foreach ($data as $identity) {
                if ($identity->getEntityType() === $this->_data['config']['entityType']) {
                    $categoryIds[] = $identity->getEntityId();
                }
            }
            return implode(',', $categoryIds);
        }
        return '';
    }
}
