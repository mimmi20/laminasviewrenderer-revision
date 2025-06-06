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

use Laminas\View\Renderer\PhpRenderer;
use Mimmi20\LaminasView\Revision\MinifyInterface;
use Override;
use PHPUnit\Event\NoPreviousThrowableException;
use PHPUnit\Framework\Exception;
use PHPUnit\Framework\TestCase;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\ContainerInterface;
use Psr\Container\NotFoundExceptionInterface;

final class RevisionHeadScriptFactoryTest extends TestCase
{
    private RevisionHeadScriptFactory $object;

    /** @throws void */
    #[Override]
    protected function setUp(): void
    {
        $this->object = new RevisionHeadScriptFactory();
    }

    /**
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     * @throws Exception
     * @throws NoPreviousThrowableException
     * @throws \PHPUnit\Framework\MockObject\Exception
     */
    public function testInvoke(): void
    {
        $minify   = $this->createMock(MinifyInterface::class);
        $renderer = $this->createMock(PhpRenderer::class);

        $container = $this->createMock(ContainerInterface::class);
        $matcher   = self::exactly(2);
        $container->expects($matcher)
            ->method('get')
            ->willReturnCallback(
                static function (string $name, array | null $options = null) use ($matcher, $minify, $renderer): mixed {
                    match ($matcher->numberOfInvocations()) {
                        1 => self::assertSame(MinifyInterface::class, $name),
                        default => self::assertSame(PhpRenderer::class, $name),
                    };

                    self::assertNull($options);

                    return match ($matcher->numberOfInvocations()) {
                        1 => $minify,
                        default => $renderer,
                    };
                },
            );
        $container->expects(self::never())
            ->method('has');

        self::assertInstanceOf(RevisionHeadScript::class, ($this->object)($container, 'test'));
    }
}
