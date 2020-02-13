<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Magento\AdobeIms\Test\Unit\Observer;

use Magento\Authorization\Model\Role;
use Magento\Framework\App\RequestInterface;

/**
 * Flush users tokens observer tests
 */
class FlushUsersTokensObserverTest extends \PHPUnit\Framework\TestCase
{
    /** @var \Magento\AdobeIms\Model\FlushUserTokens|\PHPUnit_Framework_MockObject_MockObject */
    protected $flushUserTokens;

    /** @var \Magento\AdobeIms\Observer\FlushUsersTokensObserver */
    protected $model;

    protected function setUp()
    {
        $this->flushUserTokens = $this->createMock(\Magento\AdobeIms\Model\FlushUserTokens::class);
        $helper = new \Magento\Framework\TestFramework\Unit\Helper\ObjectManager($this);
        $this->model = $helper->getObject(
            \Magento\AdobeIms\Observer\FlushUsersTokensObserver::class,
            [
                'flushUserTokens' => $this->flushUserTokens
            ]
        );
    }

    /**
     * Test flush tokens observer
     */
    public function testFlushUsersTokensObserver()
    {
        /** @var \Magento\Framework\Event\Observer|\PHPUnit_Framework_MockObject_MockObject $eventObserverMock */
        $eventObserverMock = $this->createMock(\Magento\Framework\Event\Observer::class);
        $requestMock = $this->createMock(RequestInterface::class);
        $requestMock->expects($this->once())->method("getParam")->willReturn(["Magento_AnyModule::anything"]);
        $roleMock = $this->createMock(Role::class);
        $roleMock->expects($this->once())->method("getRoleUsers")->willReturn([1,2,3]);
        $eventObserverMock->expects($this->exactly(2))->method("getDataByKey")
            ->will($this->returnValueMap([["request", $requestMock],["object", $roleMock]]));
        $this->flushUserTokens->expects($this->exactly(3))->method("execute")->willReturnSelf();
        $this->model->execute($eventObserverMock);
    }
}
