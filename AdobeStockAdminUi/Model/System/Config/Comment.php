<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

declare(strict_types=1);

namespace Magento\AdobeStockAdminUi\Model\System\Config;

use Magento\Config\Model\Config\CommentInterface;
use Magento\Framework\UrlInterface;

/**
 * Used for showing an information about how and where the Adobe Stock account can be configured
 */
class Comment implements CommentInterface
{
    private const REDIRECT_MCA = 'adobe_ims/oauth/callback';

    private const REG_EXP_URL = '.*';

    /**
     * @var UrlInterface
     */
    private $url;

    /**
     * Comment constructor.
     *
     * @param UrlInterface $url
     */
    public function __construct(
        UrlInterface $url
    ) {
        $this->url = $url;
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

        $redirectUri = __('Redirect URI: %redirect_uri', ['redirect_uri' => $this->getRedirectUrl()]);
        $pattern = __('Pattern: %pattern', ['pattern' => $this->getRedirectUrlPattern()]);
        $notes = $redirectUri . '<br><br>' . $pattern;
        return $message . '<br><br>' . $notes;
    }

    /**
     * Redirect URL for the authentication callback
     *
     * @return string
     */
    private function getRedirectUrl(): string
    {
        return $this->url->getRouteUrl(self::REDIRECT_MCA);
    }

    /**
     * Redirect URL pattern for the authentication callback
     *
     * @return string
     */
    private function getRedirectUrlPattern(): string
    {
        return str_replace('.', '\\\.', $this->getRedirectUrl()) . self::REG_EXP_URL;
    }
}
