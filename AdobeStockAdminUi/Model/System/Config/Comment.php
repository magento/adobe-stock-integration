<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

declare(strict_types=1);

namespace Magento\AdobeStockAdminUi\Model\System\Config;

use Magento\Config\Model\Config\CommentInterface;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\UrlInterface;
use Magento\Store\Model\Store;

/**
 * Class Comment
 */
class Comment implements CommentInterface
{
    private const REDIRECT_MCA = 'adobe_ims/oauth/callback';

    /**
     * @var UrlInterface
     */
    private $url;

    /**
     * @var ScopeConfigInterface
     */
    private $scopeConfig;

    /**
     * Comment constructor.
     *
     * @param UrlInterface         $url
     * @param ScopeConfigInterface $scopeConfig
     */
    public function __construct(
        UrlInterface $url,
        ScopeConfigInterface $scopeConfig
    ) {
        $this->url = $url;
        $this->scopeConfig = $scopeConfig;
    }

    /**
     * Added Redirect URL and Redirect url pattern in comment text
     *
     * @param  string $elementValue
     * @return string
     */
    public function getCommentText($elementValue): string
    {
        $message = __(
            'Configure an Adobe Stock account on the %io_link site to retrieve a Private key (Client secret).',
            ['io_link' => '<a href="https://console.adobe.io/" target="_blank">Adobe.io</a>']
        );

        $notes = __(
            'Redirect URI: %uri <br><br>Pattern: %pattern',
            [
                'uri' => $this->getRedirectUrl(),
                'pattern' => $this->getRedirectUrlPattern(),
            ]
        );

        return $message . '<br><br>' . $notes;
    }

    /**
     * Redirect URL for the authentication callback
     *
     * @return string
     */
    private function getRedirectUrl(): string
    {
        $adminRoteUrl = $this->url->getRouteUrl('admin');
        $indexPhpToUrl = $this->scopeConfig->isSetFlag(Store::XML_PATH_USE_REWRITES) ? '' : 'index.php/';
        $redirectUrl = $adminRoteUrl . $indexPhpToUrl . self::REDIRECT_MCA;

        return $redirectUrl;
    }

    /**
     * Redirect URL pattern for the authentication callback
     *
     * @return string
     */
    private function getRedirectUrlPattern(): string
    {
        return $this->getRedirectUrl();
    }
}
