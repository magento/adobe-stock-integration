<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

declare(strict_types=1);

namespace Magento\AdobeStockAsset\Model\System\Config;

use Magento\Config\Model\Config\CommentInterface;
use Magento\Framework\UrlInterface;

/**
 * Class Comment
 *
 * @package Magento\AdobeStockAsset\Model\System\Config
 */
class Comment implements CommentInterface
{
    private const REDIRECT_MCA = 'adobe_stock/oauth/callback';

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
    public function getCommentText($elementValue)
    {
        $message = sprintf(
            'Configure an Adobe Stock account on the %s site to retrieve an Private key (Client secret).',
            '<a href="https://console.adobe.io/" target="_blank">Adobe.io</a>'
        );

        $notes = sprintf(
            'Redirect URI: %s <br> Pattern: %s',
            $this->getRedirectUrl(),
            $this->getRedirectUrlPattern()
        );

        return $message . '<br>' . $notes;
    }

    /**
     * Redirect URL for the authentication callback
     *
     * @return string
     */
    private function getRedirectUrl()
    {
        return $this->url->getUrl(self::REDIRECT_MCA);
    }

    /**
     * Redirect URL pattern for the authentication callback
     *
     * @return string
     */
    private function getRedirectUrlPattern()
    {
        return str_replace('.', '\\.', $this->getRedirectUrl());
    }
}
