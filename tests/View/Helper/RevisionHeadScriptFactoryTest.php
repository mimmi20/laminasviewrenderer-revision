<?php
/**
 * This file is part of the mimmi20/laminasviewrenderer-revision package.
 *
 * Copyright (c) 2023, Thomas Mueller <mimmi20@live.de>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types = 1);

namespace Mimmi20\LaminasView\Revision\View\Helper;

use Interop\Container\ContainerInterface;
use Mimmi20\LaminasView\Revision\MinifyInterface;
use PHPUnit\Framework\TestCase;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;

final class RevisionHeadScriptFactoryTest extends TestCase
{
    private RevisionHeadScriptFactory $object;

    /** @throws void */
    protected function setUp(): void
    {
        $this->object = new RevisionHeadScriptFactory();
    }

    /**
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    public function testInvoke(): void
    {
        $minify = $this->createMock(MinifyInterface::class);

        $container = $this->getMockBuilder(ContainerInterface::class)->getMock();
        $container->expects(self::once())
            ->method('get')
            ->with(MinifyInterface::class)
            ->willReturn($minify);

        self::assertInstanceOf(RevisionHeadScript::class, ($this->object)($container, 'test'));
    }
}
