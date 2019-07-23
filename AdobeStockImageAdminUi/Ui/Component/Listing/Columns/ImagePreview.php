<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

declare(strict_types=1);

namespace Magento\AdobeStockImageAdminUi\Ui\Component\Listing\Columns;

use Exception;
use Magento\AdobeStockAssetApi\Api\UserProfileRepositoryInterface;
use Magento\AdobeStockClient\Model\Config;
use Magento\Authorization\Model\UserContextInterface;
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
     * @var UserProfileRepositoryInterface
     */
    private $userProfileRepository;

    /**
     * @var UserContextInterface
     */
    private $userContext;

    /**
     * @var Config
     */
    private $config;

    /**
     * ImagePreview constructor.
     * @param ContextInterface $context
     * @param UiComponentFactory $uiComponentFactory
     * @param UserContextInterface $userContext
     * @param UserProfileRepositoryInterface $userProfileRepository
     * @param Config $config
     * @param array $components
     * @param array $data
     */
    public function __construct(
        ContextInterface $context,
        UiComponentFactory $uiComponentFactory,
        UserContextInterface $userContext,
        UserProfileRepositoryInterface $userProfileRepository,
        Config $config,
        array $components = [],
        array $data = []
    ) {
        parent::__construct($context, $uiComponentFactory, $components, $data);

        $this->userContext = $userContext;
        $this->userProfileRepository = $userProfileRepository;
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
                    'authConfig' => [
                        'windowParams' => [
                            'width' => self::POPUP_WIDTH,
                            'height' => self::POPUP_HEIGHT
                        ]
                    ]
                ],
                (array)$this->getData('config'),
                [
                    'authConfig' => [
                        'url' => $this->getAuthUrl(),
                        'isAuthorized' => $this->isAuthorized()
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
    private function getAuthUrl(): string
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

    /**
     * Is authorized a user
     *
     * @return bool
     */
    private function isAuthorized(): bool
    {
        try {
            $userProfile = $this->userProfileRepository->getByUserId(
                (int)$this->userContext->getUserId()
            );

            $isAuthorized = !empty($userProfile->getId()) && !empty($userProfile->getAccessToken());
        } catch (Exception $e) {
            $isAuthorized = false;
        }

        return $isAuthorized;
    }
}
