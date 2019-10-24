<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Magento\AdobeIms\Test\Unit\Model;

use Magento\AdobeIms\Model\ResourceModel\UserProfile as ResourceUserProfile;
use Magento\AdobeIms\Model\UserProfile;
use Magento\AdobeIms\Model\UserProfileRepository;
use Magento\AdobeImsApi\Api\Data\UserProfileInterfaceFactory;
use Magento\Framework\TestFramework\Unit\Helper\ObjectManager;
use PHPUnit\Framework\TestCase;
use PHPUnit_Framework_MockObject_MockObject as MockObject;

/**
 * User repository test.
 */
class UserProfileRepositoryTest extends TestCase
{
    /**
     * @var ObjectManager
     */
    private $objectManager;

    /**
     * @var UserProfileRepository $model
     */
    private $model;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject $resource
     */
    private $resource;

    /**
     * @var UserProfileInterfaceFactory|MockObject $entityFactory
     */
    private $entityFactory;

    /**
     * Prepare test objects.
     */
    public function setUp(): void
    {
        $this->objectManager = new ObjectManager($this);
        $this->resource = $this->getMockBuilder(ResourceUserProfile::class)
            ->disableOriginalConstructor()
            ->setMethods(['save', 'load'])
            ->getMock();
        $this->entityFactory =  $this->getMockBuilder(UserProfileInterfaceFactory::class)
            ->disableOriginalConstructor()
            ->getMock();
        $this->model = new UserProfileRepository(
            $this->resource,
            $this->entityFactory
        );
    }

    /**
     * Test save.
     */
    public function testSave(): void
    {
        $userProfile = $this->objectManager->getObject(UserProfile::class);
        $this->assertNull($this->model->save($userProfile));
    }

    /**
     * Test save with exception.
     *
     * @expectedException \Magento\Framework\Exception\CouldNotSaveException
     * @expectedExceptionMessage Could not save user profile.
     */
    public function testSaveWithException(): void
    {
        $userProfile =  $this->getMockBuilder(UserProfile::class)->disableOriginalConstructor()->getMock();
        $this->resource->expects($this->once())
            ->method('save')
            ->with($userProfile)
            ->willThrowException(
                new \Magento\Framework\Exception\CouldNotSaveException(__('Could not save user profile.'))
            );
        $this->model->save($userProfile);
    }

    /**
     * Test get  id.
     */
    public function testGet()
    {
        $entity = $this->objectManager->getObject(UserProfile::class)->setId(1);
        $this->entityFactory->method('create')
            ->willReturn($entity);
        $this->assertEquals($this->model->get(1)->getId(), 1);
    }

    /**
     * Test get user id with exception.
     *
     * @expectedException \Magento\Framework\Exception\NoSuchEntityException
     * @expectedExceptionMessage The user profile wasn't found.
     */
    public function testGeWithException()
    {
        $entity = $this->objectManager->getObject(UserProfile::class);
        $this->entityFactory->method('create')
            ->willReturn($entity);
        $this->resource->expects($this->once())
            ->method('load')
            ->willThrowException(
                new \Magento\Framework\Exception\NoSuchEntityException(__('The user profile wasn\'t found.'))
            );
        $this->model->get(1);
    }

    /**
     * Test get by user id.
     */
    public function testGetByUserId()
    {
        $entity = $this->objectManager->getObject(UserProfile::class)->setId(1);
        $this->entityFactory->method('create')
            ->willReturn($entity);
        $this->assertEquals($this->model->getByUserId(1)->getId(), 1);
    }
}
