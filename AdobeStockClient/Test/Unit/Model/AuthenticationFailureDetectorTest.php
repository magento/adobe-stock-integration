<?php
/**
 * Copyright 2026 Adobe
 * All Rights Reserved.
 */
declare(strict_types=1);

namespace Magento\AdobeStockClient\Test\Unit\Model;

use Magento\AdobeStockClient\Model\AuthenticationFailureDetector;
use Magento\Framework\Exception\AuthenticationException;
use Magento\Framework\Exception\IntegrationException;
use PHPUnit\Framework\TestCase;

class AuthenticationFailureDetectorTest extends TestCase
{
    /**
     * @var AuthenticationFailureDetector
     */
    private AuthenticationFailureDetector $detector;

    protected function setUp(): void
    {
        $this->detector = new AuthenticationFailureDetector();
    }

    public function testMapsLegacyApiKeyInvalidMessage(): void
    {
        $exception = new \Exception('Api Key is invalid');
        $result = $this->detector->mapIntegrationException(
            new IntegrationException(__('Error'), $exception)
        );

        $this->assertInstanceOf(AuthenticationException::class, $result);
        $this->assertStringContainsString(
            'Failed to authenticate to Adobe Stock API',
            (string) $result->getMessage()
        );
    }

    public function testMapsHttp401Code(): void
    {
        $exception = new \Exception('Request failed', 401);
        $this->assertTrue($this->detector->isAuthenticationFailure($exception));
    }

    public function testDoesNotMapUnrelatedIntegrationError(): void
    {
        $exception = new IntegrationException(
            __('Failed to retrieve Adobe Stock search files results: timeout'),
            null,
            0
        );

        $this->assertNull($this->detector->mapIntegrationException($exception));
    }
}
