<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

declare(strict_types=1);

namespace Magento\AdobeIms\Block\Adminhtml;

use Magento\AdobeIms\Model\Config;
use Magento\Backend\Block\Template;
use Magento\Backend\Block\Template\Context;
use Magento\Authorization\Model\UserContextInterface;
use Magento\AdobeImsApi\Api\UserAuthorizedInterface;
use Magento\Framework\Serialize\Serializer\Json;

/**
 * Adobe Stock sign in block
 */
class SignIn extends Template
{

    /**
     * @var Config
     */
    private $config;

    /**
     * @var UserContextInterface
     */
    private $userContext;

    /**
     * @var UserAuthorizedInterface
     */
    private $userAuthorize;

    /**
     * Json Serializer Instance
     *
     * @var Json
     */
    private $serializer;

    /**
     * SignIn constructor.
     *
     * @param Config $config
     * @param Context $context
     * @param UserContextInterface $userContext
     * @param UserAuthorizedInterface $userAuthorize
     * @param Json $json
     * @param array $data
     */
    public function __construct(
        Config $config,
        Context $context,
        UserContextInterface $userContext,
        UserAuthorizedInterface $userAuthorize,
        Json $json,
        array $data = []
    ) {
        $this->config = $config;
        $this->userContext = $userContext;
        $this->userAuthorize = $userAuthorize;
        $this->serializer = $json;
        parent::__construct($context, $data);
    }

    /**
     * Return auth url for adobe stock.
     *
     * @return string
     */
    public function getAuthUrl(): string
    {
        return $this->config->getAuthUrl();
    }

    /**
     * Checks if user authorized.
     *
     * @return bool
     */
    public function isAuthorized(): bool
    {
        return $this->userAuthorize->execute((int)$this->userContext->getUserId());
    }
}
