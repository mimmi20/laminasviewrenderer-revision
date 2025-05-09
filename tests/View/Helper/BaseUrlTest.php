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

use Laminas\Uri\Exception\InvalidArgumentException;
use Laminas\Uri\Http;
use PHPUnit\Event\NoPreviousThrowableException;
use PHPUnit\Framework\Exception;
use PHPUnit\Framework\TestCase;

final class BaseUrlTest extends TestCase
{
    /**
     * @throws Exception
     * @throws InvalidArgumentException
     * @throws NoPreviousThrowableException
     * @throws \PHPUnit\Framework\MockObject\Exception
     */
    public function testInvokeWithoutHost(): void
    {
        $resource = '/abc';

        $uri = $this->createMock(Http::class);
        $uri->expects(self::once())
            ->method('setPath')
            ->with($resource)
            ->willReturnSelf();
        $uri->expects(self::once())
            ->method('setHost')
            ->with(null)
            ->willReturnSelf();
        $uri->expects(self::once())
            ->method('setPort')
            ->with(null)
            ->willReturnSelf();
        $uri->expects(self::once())
            ->method('setScheme')
            ->with(null)
            ->willReturnSelf();
        $uri->expects(self::once())
            ->method('setQuery')
            ->with(null)
            ->willReturnSelf();
        $uri->expects(self::once())
            ->method('toString')
            ->willReturn($resource);

        $object = new BaseUrl($uri);
        self::assertSame($resource, $object($resource, false, true));
    }

    /**
     * @throws Exception
     * @throws InvalidArgumentException
     * @throws Exception
     * @throws NoPreviousThrowableException
     * @throws \PHPUnit\Framework\MockObject\Exception
     */
    public function testInvokeWithHost(): void
    {
        $resource = '/abc';

        $uri = $this->createMock(Http::class);
        $uri->expects(self::once())
            ->method('setPath')
            ->with($resource)
            ->willReturnSelf();
        $uri->expects(self::once())
            ->method('setHost')
            ->with('testhost')
            ->willReturnSelf();
        $uri->expects(self::once())
            ->method('setPort')
            ->with(80)
            ->willReturnSelf();
        $uri->expects(self::once())
            ->method('setScheme')
            ->with('https')
            ->willReturnSelf();
        $uri->expects(self::once())
            ->method('toString')
            ->willReturn($resource);
        $uri->expects(self::once())
            ->method('getScheme')
            ->willReturn('https');
        $uri->expects(self::once())
            ->method('getHost')
            ->willReturn('testhost');
        $uri->expects(self::once())
            ->method('getPort')
            ->willReturn(80);

        $object = new BaseUrl($uri);
        self::assertSame($resource, $object($resource, true));
    }

    /**
     * @throws Exception
     * @throws InvalidArgumentException
     */
    public function testInvokeWithoutUri(): void
    {
        $resource = '/abc';

        $object = new BaseUrl(null);
        self::assertSame($resource, $object($resource, true));
    }
}
