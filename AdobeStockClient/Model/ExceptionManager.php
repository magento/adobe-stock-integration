<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Magento\AdobeStockClient\Model;

use Magento\AdobeStockClientApi\Api\ExceptionManagerInterface;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\UrlInterface;

class ExceptionManager implements ExceptionManagerInterface
{
    /**
     * @var UrlInterface
     */
    private $urlBuilder;

    /**
     * @var string
     */
    private $errorMessage = '';

    /**
     * ExceptionManager constructor.
     *
     * @param UrlInterface $urlBuilder
     */
    public function __construct(UrlInterface $urlBuilder)
    {
        $this->urlBuilder = $urlBuilder;
    }

    /**
     * @param \Exception $exception
     * @param int        $exceptionCode
     *
     * @throws LocalizedException
     */
    public function processException(\Exception $exception, int $exceptionCode = self::DEFAULT_CLIENT_EXCEPTION_CODE)
    {
        $this->prepareMessage($exceptionCode);
        $this->throwException();
    }

    private function prepareMessage(int $exceptionCode)
    {
        switch ($exceptionCode) {
            case self::FORBIDDEN_CONNECTION_ERROR_CODE:
                $this->buildForbiddenConnectionMessage();
                break;
            case self::DEFAULT_CLIENT_EXCEPTION_CODE:
            default:
                $this->buildDefaultErrorMessage();
        }
    }

    private function buildForbiddenConnectionMessage()
    {
        $url = $this->urlBuilder->getUrl('adminhtml/system_config/edit/section/system');
        $this->errorMessage = sprintf(
            'Adobe Stock API not configured. Please, proceed to <a href="%s">Configuration → System → Adobe Stock Integration.</a>',
            $url
        );
    }

    private function buildDefaultErrorMessage()
    {
        $this->errorMessage = self::DEFAULT_CLIENT_EXCEPTION_MESSAGE;
    }

    /**
     * @return string
     */
    private function getErrorMessage(): string
    {
        return $this->errorMessage;
    }

    /**
     * @throws LocalizedException
     */
    private function throwException()
    {
        $message = $this->getErrorMessage();
        throw new LocalizedException(__($message));
    }
}
