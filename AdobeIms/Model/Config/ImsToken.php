<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

declare(strict_types=1);

namespace Magento\AdobeIms\Model\Config;


/**
 * Ims token
 */
class ImsToken
{

    /**
     * @var string $accessToken
     */
    private $accessToken;

    /**
     * @var string $apiKey
     */
    private $apiKey;

    /**
     * @var string $privateKey
     */
    private $privateKey;

    /**
     * ImsToken constructor.
     *
     * @param string $apiKey
     * @param string $privateKey
     * @throws \Exception
     */
    public function __construct(
        string $apiKey,
        string $privateKey

    ) {
        $this->apiKey = $apiKey;
        $this->privateKey = $privateKey;
        $this->execute();
    }

    /**
     * @throws \Exception
     */
    private function execute()
    {

       //TODO  Access token get implementation
    }

    /**
     * Return access token.
     *
     * @return string
     * @throws \Exception
     */
    public function getAccessToken()
    {
        if ($this->accessToken !== '') {
            return $this->accessToken;
        }
    }
}
