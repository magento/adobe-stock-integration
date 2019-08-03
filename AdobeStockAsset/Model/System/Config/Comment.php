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
        $redirectUrl = $this->url->getUrl('adobe_stock/oauth/callback');
        $redirectUrlPattern = str_replace('.', '\\.', $redirectUrl);
        $commentText = "Configure an Adobe Stock account on the 
        <a href=\"https://console.adobe.io/\" target=\"_blank\">Adobe.io</a> 
        site to retrieve an Private key (Client secret).</br> 
        Note: the Default redirect URI is {$redirectUrl} and the redirect URI pattern is {$redirectUrlPattern}.";
        return $commentText;
    }
}
