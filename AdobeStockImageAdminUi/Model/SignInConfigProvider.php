<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Magento\AdobeStockImageAdminUi\Model;

use Magento\AdobeImsApi\Api\ConfigProviderInterface;
use Magento\AdobeImsApi\Api\UserAuthorizedInterface;
use Magento\AdobeStockClientApi\Api\ClientInterface;
use Magento\Framework\Exception\AuthenticationException;
use Magento\Framework\Exception\AuthorizationException;
use Magento\Framework\Exception\IntegrationException;
use Magento\Framework\UrlInterface;

/**
 * Adobe Stock sign in block
 */
class SignInConfigProvider implements ConfigProviderInterface
{
    /**
     * @var UserAuthorizedInterface
     */
    private $userAuthorized;

    /**
     * @var ClientInterface
     */
    private $client;

    /**
     * @var UrlInterface
     */
    private $url;

    /**
     * SignIn constructor.
     * @param ClientInterface $client
     * @param UserAuthorizedInterface $userAuthorized
     * @param UrlInterface $url
     */
    public function __construct(
        ClientInterface $client,
        UserAuthorizedInterface $userAuthorized,
        UrlInterface $url
    ) {
        $this->userAuthorized = $userAuthorized;
        $this->client = $client;
        $this->url = $url;
    }

    /**
     * @inheritdoc
     */
    public function get(): array
    {
        return [
            'component' => 'Magento_AdobeStockImageAdminUi/js/signIn',
            'template' => 'Magento_AdobeStockImageAdminUi/signIn',
            'userQuota' => $this->getUserQuota(),
            'quotaUrl' => $this->url->getUrl('adobe_stock/license/quota')
        ];
    }

    /**
     * Get user quota information
     *
     * @return array
     * @throws IntegrationException
     */
    private function getUserQuota(): array
    {
        $defaultQuota = [
            'images' => 0,
            'credits' => 0
        ];
        if (!$this->userAuthorized->execute()) {
            return $defaultQuota;
        }
        try {
            $quota = $this->client->getQuota();
            return [
                'images' => $quota->getImages(),
                'credits' => $quota->getCredits()
            ];
        } catch (AuthenticationException | AuthorizationException $exception) {
            return $defaultQuota;
        }
    }
}
