<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Magento\AdobeStockClient\Test\Unit\Model;

use AdobeStock\Api\Models\SearchParameters;
use Magento\AdobeStockClient\Model\SearchParametersProviderComposite;
use Magento\AdobeStockClient\Model\SearchParameterProviderInterface;
use Magento\Framework\Api\SearchCriteriaInterface;
use Magento\Framework\TestFramework\Unit\Helper\ObjectManager;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

/**
 * Test for search  parameters provider.
 */
class SearchParametersProviderCompositeTest extends TestCase
{
    /**
     * @var ObjectManager
     */
    private $objectManager;

    /**
     * @var SearchParametersProviderComposite
     */
    private $searchParametersProviderComposite;

    /**
     * @var SearchParameterProviderInterface|MockObject
     */
    private $searchParametersProviderMock;

    /**
     * Prepare test objects.
     */
    protected function setUp(): void
    {
        $this->objectManager = new ObjectManager($this);
        $this->searchParametersProviderMock =  $this->createMock(SearchParameterProviderInterface::class);
        $this->searchParametersProviderComposite = $this->objectManager->getObject(
            SearchParametersProviderComposite::class,
            [
                'providers' => [$this->searchParametersProviderMock]
            ]
        );
    }

    /**
     * Test apply search criteria to SearchParameters.
     */
    public function testApply(): void
    {
        /** @var SearchCriteriaInterface $searchCriteriaMock */
        $searchCriteriaMock = $this->createMock(SearchCriteriaInterface::class);
        /** @var SearchParameters $searchParamsMock */
        $searchParamsMock = $this->createMock(SearchParameters::class);
        $this->searchParametersProviderMock->expects($this->once())
            ->method('apply')
            ->with($searchCriteriaMock, $searchParamsMock);
        $methodResult = $this->searchParametersProviderComposite->apply($searchCriteriaMock, $searchParamsMock);
        $this->assertInstanceOf(SearchParameters::class, $methodResult);
    }
}
