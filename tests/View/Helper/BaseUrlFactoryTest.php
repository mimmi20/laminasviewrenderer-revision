<?php

/**
 * This file is part of the mimmi20/laminasviewrenderer-revision package.
 *
 * Copyright (c) 2023-2025, Thomas Mueller <mimmi20@live.de>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types = 1);

namespace Mimmi20\LaminasView\Revision\View\Helper;

use Laminas\Http\PhpEnvironment\Request;
use Laminas\Uri\Http;
use Override;
use PHPUnit\Framework\TestCase;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\ContainerInterface;
use Psr\Container\NotFoundExceptionInterface;

final class BaseUrlFactoryTest extends TestCase
{
    private BaseUrlFactory $object;

    /** @throws void */
    #[Override]
    protected function setUp(): void
    {
        $this->object = new BaseUrlFactory();
    }

    /**
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    public function testInvokeHasRequest(): void
    {
        $uri = $this->createMock(Http::class);

        $request = $this->createMock(Request::class);
        $request->expects(self::once())
            ->method('getUri')
            ->willReturn($uri);

        $container = $this->createMock(ContainerInterface::class);
        $container->expects(self::once())
            ->method('has')
            ->with('Request')
            ->willReturn(true);
        $container->expects(self::once())
            ->method('get')
            ->with('Request')
            ->willReturn($request);

        self::assertInstanceOf(BaseUrl::class, ($this->object)($container, 'test'));
    }

    /**
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    public function testInvokeHasNoRequest(): void
    {
        $request = $this->createMock(Request::class);
        $request->expects(self::never())
            ->method('getUri');

        $container = $this->createMock(ContainerInterface::class);
        $container->expects(self::once())
            ->method('has')
            ->with('Request')
            ->willReturn(false);
        $container->expects(self::never())
            ->method('get');

        self::assertInstanceOf(BaseUrl::class, ($this->object)($container, 'test'));
    }
}
