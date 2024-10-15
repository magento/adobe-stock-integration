<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

declare(strict_types=1);

namespace Magento\AdobeStockImageAdminUi\Ui\Component\Listing\Filter;

use Magento\Framework\Api\FilterBuilder;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\View\Element\UiComponent\ContextInterface;
use Magento\Framework\View\Element\UiComponentFactory;
use Magento\Ui\Component\Filters\FilterModifier;
use Magento\Ui\Component\Filters\Type\AbstractFilter;
use Magento\Ui\Component\Filters\Type\Input;
use Magento\Ui\Component\Form\Element\ColorPicker;
use Magento\Ui\Model\ColorPicker\ColorModesProvider;
use Magento\Ui\View\Element\BookmarkContextProviderInterface;

/**
 * Color grid filter
 */
class Color extends AbstractFilter
{
    public const NAME = 'filter_input';

    /**
     * Provides color picker modes configuration
     *
     * @var ColorModesProvider
     */
    private $modesProvider;

    /**
     * Color constructor.
     * @param ContextInterface $context
     * @param UiComponentFactory $uiComponentFactory
     * @param FilterBuilder $filterBuilder
     * @param FilterModifier $filterModifier
     * @param ColorModesProvider $modesProvider
     * @param array $components
     * @param array $data
     * @param BookmarkContextProviderInterface|null $bookmarkContextProvider
     */
    public function __construct(
        ContextInterface $context,
        UiComponentFactory $uiComponentFactory,
        FilterBuilder $filterBuilder,
        FilterModifier $filterModifier,
        ColorModesProvider $modesProvider,
        array $components = [],
        array $data = [],
        BookmarkContextProviderInterface $bookmarkContextProvider = null
    ) {
        $this->modesProvider = $modesProvider;
        parent::__construct(
            $context,
            $uiComponentFactory,
            $filterBuilder,
            $filterModifier,
            $components,
            $data,
            $bookmarkContextProvider
        );
    }

    /**
     * Prepare component configuration
     *
     * @return void
     * @throws LocalizedException
     */
    public function prepare(): void
    {
        $wrappedComponent = $this->uiComponentFactory->create(
            $this->getName(),
            Input::COMPONENT,
            ['context' => $this->getContext()]
        );
        $wrappedComponent->prepare();
        // Merge JS configuration with wrapped component configuration
        $jsConfig = array_replace_recursive(
            $this->getJsConfig($wrappedComponent),
            $this->getJsConfig($this)
        );
        $this->setData('js_config', $jsConfig);

        $this->setData(
            'config',
            array_replace_recursive(
                (array)$wrappedComponent->getData('config'),
                (array)$this->getData('config')
            )
        );

        $this->applyFilter();

        $this->initColorPickerConfig();

        parent::prepare();
    }

    /**
     * Initialize color picker configuration
     */
    private function initColorPickerConfig(): void
    {
        $mode = $this->getData('config/colorPickerMode') ?? ColorPicker::DEFAULT_MODE;
        $colorPickerMode = $this->modesProvider->getModes()[$mode];
        $colorPickerMode['preferredFormat'] = $this->getData('config/colorFormat');
        $this->_data['config']['colorPickerConfig'] = $colorPickerMode;
    }

    /**
     * Apply filter
     */
    private function applyFilter(): void
    {
        if (!isset($this->filterData[$this->getName()])) {
            return;
        }

        $value = str_replace(['%', '_', '#'], ['\%', '\_', ''], $this->filterData[$this->getName()]);

        if ($value || $value === '0') {
            $filter = $this->filterBuilder->setConditionType('like')
                ->setField($this->getName())
                ->setValue($value)
                ->create();

            $this->getContext()->getDataProvider()->addFilter($filter);
        }
    }
}
