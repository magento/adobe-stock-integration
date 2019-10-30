<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Magento\AdobeMediaGallery\Test\Unit\Model\Keyword\Command;

use Magento\AdobeMediaGallery\Model\Keyword\Command\SaveAssetLinks;
use Magento\Framework\App\ResourceConnection;
use Magento\Framework\DB\Adapter\Pdo\Mysql;
use Magento\Framework\Exception\CouldNotSaveException;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

/**
 * SaveAssetLinksTest.
 */
class SaveAssetLinksTest extends TestCase
{
    /**
     * @var SaveAssetLinks
     */
    private $sut;

    /**
     * @var ResourceConnection|MockObject
     */
    private $resourceConnectionMock;

    /**
     * @var AdapterInterface|MockObject
     */
    private $connectionMock;

    /**
     * Prepare test objects.
     */
    public function setUp(): void
    {
        $this->resourceConnectionMock = $this->createMock(ResourceConnection::class);
        $this->connectionMock = $this->getMockBuilder(Mysql::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->sut = new SaveAssetLinks(
            $this->resourceConnectionMock
        );
    }

    /**
     * Test saving the asset keyword links
     *
     * @dataProvider assetLinksDataProvider
     *
     * @param int $assetId
     * @param array $keywordIds
     * @param array $values
     */
    public function testAssetKeywordsSave(int $assetId, array $keywordIds, array $values): void
    {
        $this->resourceConnectionMock->expects($this->once())
            ->method('getConnection')
            ->willReturn($this->connectionMock);
        $this->connectionMock->expects($this->once())
            ->method('insertArray')
            ->with(
                'media_gallery_asset_keyword',
                ['asset_id', 'keyword_id'],
                $values,
                2
            );

        $this->sut->execute($assetId, $keywordIds);
    }

    /**
     * Testing throwing exception handling
     *
     * @throws CouldNotSaveException
     */
    public function testAssetNotSavingCausedByError(): void
    {
        $this->resourceConnectionMock
            ->method('getConnection')
            ->willThrowException((new \Exception()));
        $this->expectException(CouldNotSaveException::class);
        $this->expectExceptionMessage('An error occurred during save asset keyword links: ');

        $this->sut->execute(1, [1, 2]);
    }

    /**
     * Providing asset links
     *
     * @return array
     */
    public function assetLinksDataProvider(): array
    {
        return [
            [
                12,
                [],
                []
            ],
            [
                12,
                [1],
                [
                    [12, 1]
                ]
            ], [
                12,
                [1, 2],
                [
                    [12, 1],
                    [12, 2],
                ]
            ]
        ];
    }
}
