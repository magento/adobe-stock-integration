<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

declare(strict_types=1);

namespace Magento\AdobeIms\Test\Unit\Model;

use Magento\AdobeIms\Model\UserProfile;
use Magento\AdobeImsApi\Api\Data\UserProfileExtensionInterface;
use Magento\Framework\TestFramework\Unit\Helper\ObjectManager;
use PHPUnit\Framework\TestCase;

/**
 * User profile test.
 *
 * Tests all setters and getters of data transport class
 */
class UserProfileTest extends TestCase
{
    /**
     * @var ObjectManager
     */
    private $objectManager;

    /**
     * @var UserProfile $model
     */
    private $model;

    /**
     * Prepare test object.
     */
    protected function setUp(): void
    {
        $this->objectManager = new ObjectManager($this);
        $this->model = $this->objectManager->getObject(UserProfile::class);
    }

    /**
     * Test setAccessToken
     *
     * @param string|null $value
     * @testWith ["value1"]
     *           ["value2"]
     */
    public function testAccessToken(
        ?string $value
    ): void {
        $this->model->setAccessToken($value);
        $this->assertSame($value, $this->model->getAccessToken());
    }

    /**
     * Test setRefreshToken
     *
     * @param string|null $value
     * @testWith ["value1"]
     *           ["value2"]
     */
    public function testRefreshToken(
        ?string $value
    ): void {
        $this->model->setRefreshToken($value);
        $this->assertSame($value, $this->model->getRefreshToken());
    }

    /**
     * Test setAccessTokenExpiresAt
     *
     * @param string|null $value
     * @testWith ["value1"]
     *           ["value2"]
     */
    public function testAccessTokenExpiresAt(
        ?string $value
    ): void {
        $this->model->setAccessTokenExpiresAt($value);
        $this->assertSame($value, $this->model->getAccessTokenExpiresAt());
    }

    /**
     * Test setCreatedAt
     *
     * @param string|null $value
     * @testWith ["value1"]
     *           ["value2"]
     */
    public function testCreatedAt(
        ?string $value
    ): void {
        $this->model->setCreatedAt($value);
        $this->assertSame($value, $this->model->getCreatedAt());
    }

    /**
     * Test setUpdatedAt
     *
     * @param string|null $value
     * @testWith ["value1"]
     *           ["value2"]
     */
    public function testUpdatedAt(
        ?string $value
    ): void {
        $this->model->setUpdatedAt($value);
        $this->assertSame($value, $this->model->getUpdatedAt());
    }

    /**
     * Test setAccountType
     *
     * @param string|null $value
     * @testWith ["value1"]
     *           ["value2"]
     */
    public function testAccountType(
        ?string $value
    ): void {
        $this->model->setAccountType($value);
        $this->assertSame($value, $this->model->getAccountType());
    }

    /**
     * Test setEmail
     *
     * @param string|null $value
     * @testWith ["value1"]
     *           ["value2"]
     */
    public function testEmail(
        ?string $value
    ): void {
        $this->model->setEmail($value);
        $this->assertSame($value, $this->model->getEmail());
    }

    /**
     * Test setImage
     *
     * @param string|null $value
     * @testWith ["value1"]
     *           ["value2"]
     */
    public function testImage(
        ?string $value
    ): void {
        $this->model->setImage($value);
        $this->assertSame($value, $this->model->getImage());
    }

    /**
     * Test setName
     *
     * @param string|null $value
     * @testWith ["value1"]
     *           ["value2"]
     */
    public function testName(
        ?string $value
    ): void {
        $this->model->setName($value);
        $this->assertSame($value, $this->model->getName());
    }

    /**
     * Test setUserId
     *
     * @param int|null $value
     * @testWith [1]
     *           [2]
     */
    public function testUserId(
        ?int $value
    ): void {
        $this->model->setUserId($value);
        $this->assertSame($value, $this->model->getUserId());
    }

    /**
     * Test setExtensionAttributes
     */
    public function testExtensionAttributes(): void
    {
        $value = $this->createMock(UserProfileExtensionInterface::class);
        $this->model->setExtensionAttributes($value);
        $this->assertSame($value, $this->model->getExtensionAttributes());
    }
}
