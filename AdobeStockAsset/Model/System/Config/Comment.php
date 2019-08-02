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
     * @param UrlInterface $url
     */
    public function __construct(
        UrlInterface $url
    ) {
        $this->url = $url;
    }

    /**
     * @param string $elementValue
     * @return string
     */
    public function getCommentText($elementValue)
    {
        $redirect_url = $this->url->getUrl('adobe_stock/oauth/callback');
        $redirect_url_pattern = $this->url->getUrl('admin/adobe_stock/oauth/callback/');
        $commentText = sprintf("Configure an Adobe Stock account on the <a href=\"https://console.adobe.io/\" target=\"_blank\">Adobe.io</a> site to retrieve an Private key (Client secret).</br>
        In order to create an integration in console.adobe.com Magento admin needs to enter %s and %s.", $redirect_url, $redirect_url_pattern);
        return $commentText;
    }
}
