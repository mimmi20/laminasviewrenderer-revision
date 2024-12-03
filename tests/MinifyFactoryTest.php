<?php

/**
 * This file is part of the mimmi20/laminasviewrenderer-revision package.
 *
 * Copyright (c) 2023-2024, Thomas Mueller <mimmi20@live.de>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types = 1);

namespace Mimmi20\LaminasView\Revision;

use JsonException;
use Mimmi20\LaminasView\Revision\Config\MinifyConfigInterface;
use PHPUnit\Framework\TestCase;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\ContainerInterface;

final class MinifyFactoryTest extends TestCase
{
    private MinifyFactory $object;

    /** @throws void */
    protected function setUp(): void
    {
        $this->object = new MinifyFactory();
    }

    /**
     * @throws ContainerExceptionInterface
     * @throws JsonException
     */
    public function testInvoke(): void
    {
        $minifyConfig = $this->createMock(MinifyConfigInterface::class);
        $minifyConfig->expects(self::once())
            ->method('isEnabled')
            ->willReturn(true);
        $minifyConfig->expects(self::once())
            ->method('getRevision')
            ->willReturn(null);

        $container = $this->createMock(ContainerInterface::class);
        $container->expects(self::once())
            ->method('get')
            ->with(MinifyConfigInterface::class)
            ->willReturn($minifyConfig);

        self::assertInstanceOf(Minify::class, ($this->object)($container, 'test'));
    }

    /**
     * @throws ContainerExceptionInterface
     * @throws JsonException
     */
    public function testInvokeWithRevisionFile(): void
    {
        $revision = '565656556';

        $minifyConfig = $this->createMock(MinifyConfigInterface::class);
        $minifyConfig->expects(self::once())
            ->method('isEnabled')
            ->willReturn(true);
        $minifyConfig->expects(self::once())
            ->method('getRevision')
            ->willReturn($revision);

        $container = $this->createMock(ContainerInterface::class);
        $container->expects(self::once())
            ->method('get')
            ->with(MinifyConfigInterface::class)
            ->willReturn($minifyConfig);

        self::assertInstanceOf(Minify::class, ($this->object)($container, 'test'));
    }
}
