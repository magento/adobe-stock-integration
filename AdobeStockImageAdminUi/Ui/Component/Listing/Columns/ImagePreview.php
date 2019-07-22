<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

declare(strict_types=1);

namespace Magento\AdobeStockImageAdminUi\Ui\Component\Listing\Columns;

use Magento\AdobeStockClient\Model\Config;
use Magento\Framework\View\Element\UiComponent\ContextInterface;
use Magento\Framework\View\Element\UiComponentFactory;
use Magento\Ui\Component\Listing\Columns\Column;

class ImagePreview extends Column
{
    /**
     * Settings of authentication popup
     */
    const POPUP_WIDTH = '500';
    const POPUP_HEIGHT = '300';

    /**
     * @var Config
     */
    private $config;

    /**
     * ImagePreview constructor.
     * @param ContextInterface $context
     * @param UiComponentFactory $uiComponentFactory
     * @param Config $config
     * @param array $components
     * @param array $data
     */
    public function __construct(
        ContextInterface $context,
        UiComponentFactory $uiComponentFactory,
        Config $config,
        array $components = [],
        array $data = []
    ) {
        parent::__construct($context, $uiComponentFactory, $components, $data);

        $this->config = $config;
    }

    /**
     * @inheritDoc
     */
    public function prepare()
    {
        parent::prepare();

        $this->setData(
            'config',
            array_replace_recursive(
                [
                    'auth' => [
                        'width' => self::POPUP_WIDTH,
                        'height' => self::POPUP_HEIGHT
                    ]
                ],
                (array)$this->getData('config'),
                [
                    'auth' => [
                        'url' => $this->getAuthUrl()
                    ]
                ]
            )
        );
    }

    /**
     * Get auth URL
     *
     * @return string
     */
    private function getAuthUrl()
    {
        return str_replace(
            [
                '#{client_id}',
                '#{scope}',
                '#{response_type}'
            ],
            [
                $this->config->getApiKey(),
                'openid',
                'code'
            ],
            $this->config->getAuthUrlPattern()
        );
    }
}
