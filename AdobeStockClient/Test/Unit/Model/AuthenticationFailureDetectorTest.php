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
    private const EXPECTED_ADMIN_MESSAGE =
        'Failed to authenticate to Adobe Stock API. Please correct the API credentials.';

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

    public function testMapsHttp403Code(): void
    {
        $exception = new \Exception('Request failed', 403);
        $this->assertTrue($this->detector->isAuthenticationFailure($exception));
    }

    public function testDoesNotMapNonAuthHttpCode(): void
    {
        $exception = new \Exception('Server error', 500);
        $this->assertFalse($this->detector->isAuthenticationFailure($exception));
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

    /**
     * @dataProvider authenticationMessageProvider
     */
    public function testRecognizesAuthenticationMessage(string $message): void
    {
        $this->assertTrue(
            $this->detector->isAuthenticationFailure(new \Exception($message)),
            sprintf('Expected message "%s" to be treated as an authentication failure.', $message)
        );
    }

    /**
     * Covers every credential/auth phrase the detector watches for, including realistic
     * Adobe Stock SDK / OAuth wording and mixed-case variants to guard the strtolower() path.
     *
     * @return array<string, array{0: string}>
     */
    public static function authenticationMessageProvider(): array
    {
        return [
            'legacy api key invalid'      => ['Api Key is invalid'],
            'api key required'            => ['Api Key is required'],
            'invalid api key (sdk)'       => ['The provided Invalid API Key cannot be used'],
            'invalid client (oauth)'      => ['Invalid client provided'],
            'invalid_client (oauth raw)'  => ['{"error":"invalid_client"}'],
            'unauthorized'                => ['Unauthorized'],
            'authentication failed'       => ['Authentication failed for user'],
            'authentication error'        => ['Authentication error occurred'],
            'access denied'               => ['Access denied for the given token'],
            'forbidden'                   => ['Forbidden'],
            'invalid credentials'         => ['Invalid credentials supplied'],
            'credential keyword'          => ['Bad credential format'],
            'not authorized'              => ['User is not authorized to access this resource'],
            'mixed case unauthorized'     => ['UNAUTHORIZED ACCESS'],
        ];
    }

    public function testRecognizesAuthenticationFailureInPreviousExceptionChain(): void
    {
        $root = new \Exception('Invalid client');
        $middle = new \Exception('SDK call failed', 0, $root);
        $top = new \Exception('Adobe Stock search wrapper error', 0, $middle);

        $this->assertTrue($this->detector->isAuthenticationFailure($top));
    }

    public function testMapsIntegrationExceptionWhenOwnMessageMatches(): void
    {
        $exception = new IntegrationException(__('Unauthorized'));

        $result = $this->detector->mapIntegrationException($exception);

        $this->assertInstanceOf(AuthenticationException::class, $result);
        $this->assertSame(self::EXPECTED_ADMIN_MESSAGE, (string) $result->getMessage());
    }

    public function testCreateAuthenticationExceptionReturnsAdminFacingMessage(): void
    {
        $result = $this->detector->createAuthenticationException();

        $this->assertInstanceOf(AuthenticationException::class, $result);
        $this->assertSame(self::EXPECTED_ADMIN_MESSAGE, (string) $result->getMessage());
    }
}
