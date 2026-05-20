<?php

/**
 * Copyright 2026 Adobe
 * All Rights Reserved.
 */
declare(strict_types=1);

namespace Magento\AdobeStockClient\Model;

use Magento\Framework\Exception\AuthenticationException;
use Magento\Framework\Exception\IntegrationException;
use Throwable;

/**
 * Detects Adobe Stock API credential failures for admin authentication messaging.
 */
class AuthenticationFailureDetector
{
    /**
     * Substrings observed in Adobe Stock SDK / API responses (legacy and current).
     *
     * @var string[]
     */
    private const MESSAGE_PATTERNS = [
        'api key is invalid',
        'api key is required',
        'invalid api key',
        'invalid client',
        'invalid_client',
        'unauthorized',
        'authentication failed',
        'authentication error',
        'access denied',
        'forbidden',
        'invalid credentials',
        'credential',
        'not authorized',
    ];

    /**
     * HTTP status codes that indicate invalid API credentials or auth failure.
     *
     * @var int[]
     */
    private const AUTH_HTTP_CODES = [401, 403];

    /**
     * Whether the throwable chain represents invalid Adobe Stock API credentials.
     * @param Throwable $exception
     * @return bool
     */
    public function isAuthenticationFailure(Throwable $exception): bool
    {
        if (in_array((int)$exception->getCode(), self::AUTH_HTTP_CODES, true)) {
            return true;
        }

        $message = strtolower($exception->getMessage());
        foreach (self::MESSAGE_PATTERNS as $pattern) {
            if (str_contains($message, $pattern)) {
                return true;
            }
        }

        $previous = $exception->getPrevious();
        if ($previous !== null && $previous !== $exception) {
            return $this->isAuthenticationFailure($previous);
        }

        return false;
    }

    /**
     * Authentication exception shown in Adobe Stock admin UI (AC-17123).
     */
    public function createAuthenticationException(): AuthenticationException
    {
        return new AuthenticationException(__(
            'Failed to authenticate to Adobe Stock API. Please correct the API credentials.'
        ));
    }

    /**
     * Map IntegrationException to AuthenticationException when credentials are invalid.
     * @param IntegrationException $exception
     * @return AuthenticationException|null
     */
    public function mapIntegrationException(IntegrationException $exception): ?AuthenticationException
    {
        if ($this->isAuthenticationFailure($exception)) {
            return $this->createAuthenticationException();
        }

        $previous = $exception->getPrevious();
        if ($previous !== null && $this->isAuthenticationFailure($previous)) {
            return $this->createAuthenticationException();
        }

        return null;
    }
}
