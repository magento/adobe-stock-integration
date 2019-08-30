<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

declare(strict_types=1);

namespace Magento\AdobeStockImageAdminUi\Ui\Component\Listing\Columns;

use Magento\AdobeIms\Controller\Adminhtml\OAuth\Callback;
use Magento\AdobeImsApi\Api\UserProfileRepositoryInterface;
use Magento\AdobeImsApi\Api\Data\ConfigInterface;
use Magento\Authorization\Model\UserContextInterface;
use Magento\Framework\UrlInterface;
use Magento\Framework\View\Element\UiComponent\ContextInterface;
use Magento\Framework\View\Element\UiComponentFactory;
use Magento\Ui\Component\Listing\Columns\Column;
use Magento\AdobeImsApi\Api\UserAuthorizedInterface;

/**
 * Class ImagePreview
 */
class ImagePreview extends Column
{
    /**
     * @var UserProfileRepositoryInterface
     */
    private $userProfileRepository;

    /**
     * @var UserContextInterface
     */
    private $userContext;

    /**
     * @var UrlInterface
     */
    private $urlBuilder;

    /**
     * @var ConfigInterface
     */
    private $config;

    /**
     * @var UserAuthorizedInterface $userAuthorize
     */
    private $userAuthorize;

    /**
     * ImagePreview constructor.
     *
     * @param ContextInterface $context
     * @param UiComponentFactory $uiComponentFactory
     * @param UserContextInterface $userContext
     * @param UserAuthorizedInterface $userAuthorize
     * @param UrlInterface $urlBuilder
     * @param ConfigInterface $config
     * @param array $components
     * @param array $data
     */
    public function __construct(
        ContextInterface $context,
        UiComponentFactory $uiComponentFactory,
        UserContextInterface $userContext,
        UserAuthorizedInterface $userAuthorize,
        UrlInterface $urlBuilder,
        ConfigInterface $config,
        array $components = [],
        array $data = []
    ) {
        parent::__construct($context, $uiComponentFactory, $components, $data);

        $this->userContext = $userContext;
        $this->userAuthorize = $userAuthorize;
        $this->urlBuilder = $urlBuilder;
        $this->config = $config;
    }

    /**
     * @inheritdoc
     */
    public function prepare()
    {
        parent::prepare();

        $this->setData(
            'config',
            array_replace_recursive(
                (array)$this->getData('config'),
                [
                    'downloadImagePreviewUrl' => $this->urlBuilder->getUrl('adobe_stock/preview/download'),
                    'getQuotaUrl' => $this->urlBuilder->getUrl('adobe_stock/license/getquota'),
                    'imageSeriesUrl' => $this->urlBuilder->getUrl('adobe_stock/preview/series'),
                    'authConfig' => [
                        'url' => $this->config->getAuthUrl(),
                        'isAuthorized' => $this->isAuthorized(),
                        'response' => [
                            'regexpPattern' => Callback::RESPONSE_REGEXP_PATTERN,
                            'codeIndex' => Callback::RESPONSE_CODE_INDEX,
                            'messageIndex' => Callback::RESPONSE_MESSAGE_INDEX,
                            'successCode' => Callback::RESPONSE_SUCCESS_CODE,
                            'errorCode' => Callback::RESPONSE_ERROR_CODE
                        ]
                    ]
                ]
            )
        );
    }

    /**
     * Is authorized a user
     *
     * @return bool
     */
    private function isAuthorized(): bool
    {
        return $this->userAuthorize->execute((int)$this->userContext->getUserId());
    }
}
