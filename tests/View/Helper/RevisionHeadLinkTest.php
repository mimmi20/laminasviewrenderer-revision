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

namespace Mimmi20\LaminasView\Revision\View\Helper;

use Laminas\Uri\Exception\InvalidArgumentException;
use Laminas\View\Exception\BadMethodCallException;
use Laminas\View\Helper\Placeholder\Container\AbstractStandalone;
use Laminas\View\Renderer\PhpRenderer;
use Mimmi20\LaminasView\Revision\MinifyInterface;
use PHPUnit\Framework\TestCase;

final class RevisionHeadLinkTest extends TestCase
{
    /**
     * @throws InvalidArgumentException
     * @throws BadMethodCallException
     */
    public function testAppendPackage(): void
    {
        $package = 'test-package';

        $minify = $this->createMock(MinifyInterface::class);
        $minify
            ->expects(self::once())
            ->method('getPackageFiles')
            ->with($package)
            ->willReturn(['files' => ['abc.txt', '', 'bcd.txt']]);
        $minify
            ->expects(self::once())
            ->method('isItemOkToAddRevision')
            ->with('css', '/abc.txt')
            ->willReturn(true);
        $minify
            ->expects(self::once())
            ->method('addRevision')
            ->with('/abc.txt')
            ->willReturn('/abc_42.txt');

        $headLink = $this->createMock(AbstractStandalone::class);
        $headLink
            ->expects(self::once())
            ->method('__call')
            ->with('appendStylesheet', ['https://www.test.de/abc_42.txt', 'screen', '!IE', []]);

        $renderer = $this->createMock(PhpRenderer::class);
        $matcher  = self::exactly(4);
        $renderer
            ->expects($matcher)
            ->method('__call')
            ->willReturnCallback(
                static function (string $method, array $argv) use ($matcher, $headLink): string | AbstractStandalone {
                    match ($matcher->numberOfInvocations()) {
                        3 => self::assertSame('serverUrl', $method),
                        2 => self::assertSame('headLink', $method),
                        default => self::assertSame('baseUrl', $method),
                    };

                    match ($matcher->numberOfInvocations()) {
                        1 => self::assertSame(['abc.txt', false, false], $argv),
                        2 => self::assertSame([], $argv),
                        3 => self::assertSame(['/abc_42.txt'], $argv),
                        default => self::assertSame(['bcd.txt', false, false], $argv),
                    };

                    return match ($matcher->numberOfInvocations()) {
                        1 => '/abc.txt',
                        2 => $headLink,
                        3 => 'https://www.test.de/abc_42.txt',
                        default => '',
                    };
                },
            );
        $renderer
            ->expects(self::never())
            ->method('plugin');

        $object = new RevisionHeadLink($minify, $renderer);

        $return = $object->appendPackage($package, 'screen', '!IE', ['rel' => 'prev']);

        self::assertSame($object, $return);
    }

    /**
     * @throws InvalidArgumentException
     * @throws BadMethodCallException
     */
    public function testAppendPackage2(): void
    {
        $package = 'test-package';

        $minify = $this->createMock(MinifyInterface::class);
        $minify
            ->expects(self::once())
            ->method('getPackageFiles')
            ->with($package)
            ->willReturn([]);
        $minify
            ->expects(self::never())
            ->method('isItemOkToAddRevision');
        $minify
            ->expects(self::never())
            ->method('addRevision');

        $renderer = $this->createMock(PhpRenderer::class);
        $renderer
            ->expects(self::never())
            ->method('__call');
        $renderer
            ->expects(self::never())
            ->method('plugin');

        $object = new RevisionHeadLink($minify, $renderer);

        $return = $object->appendPackage($package, 'screen', '!IE', ['rel' => 'prev', 'async' => null]);

        self::assertSame($object, $return);
    }

    /**
     * @throws InvalidArgumentException
     * @throws BadMethodCallException
     */
    public function testAppendPackage3(): void
    {
        $package = 'test-package';

        $minify = $this->createMock(MinifyInterface::class);
        $minify
            ->expects(self::once())
            ->method('getPackageFiles')
            ->with($package)
            ->willReturn(['files' => false]);
        $minify
            ->expects(self::never())
            ->method('isItemOkToAddRevision');
        $minify
            ->expects(self::never())
            ->method('addRevision');

        $renderer = $this->createMock(PhpRenderer::class);
        $renderer
            ->expects(self::never())
            ->method('__call');
        $renderer
            ->expects(self::never())
            ->method('plugin');

        $object = new RevisionHeadLink($minify, $renderer);

        $return = $object->appendPackage($package, 'screen', '!IE', ['rel' => 'prev', 'async' => null]);

        self::assertSame($object, $return);
    }

    /**
     * @throws InvalidArgumentException
     * @throws BadMethodCallException
     */
    public function testAppendPackage4(): void
    {
        $package = 'test-package';

        $minify = $this->createMock(MinifyInterface::class);
        $minify
            ->expects(self::once())
            ->method('getPackageFiles')
            ->with($package)
            ->willReturn(['files' => []]);
        $minify
            ->expects(self::never())
            ->method('isItemOkToAddRevision');
        $minify
            ->expects(self::never())
            ->method('addRevision');

        $renderer = $this->createMock(PhpRenderer::class);
        $renderer
            ->expects(self::never())
            ->method('__call');
        $renderer
            ->expects(self::never())
            ->method('plugin');

        $object = new RevisionHeadLink($minify, $renderer);

        $return = $object->appendPackage($package, 'screen', '!IE', ['rel' => 'prev', 'async' => null]);

        self::assertSame($object, $return);
    }

    /**
     * @throws InvalidArgumentException
     * @throws BadMethodCallException
     */
    public function testAppendStylesheet(): void
    {
        $href = 'https://www.test.de/abc.txt';

        $minify = $this->createMock(MinifyInterface::class);
        $minify
            ->expects(self::never())
            ->method('getPackageFiles');
        $minify
            ->expects(self::once())
            ->method('isItemOkToAddRevision')
            ->with('css', $href)
            ->willReturn(true);
        $minify
            ->expects(self::once())
            ->method('addRevision')
            ->with($href)
            ->willReturn('https://www.test.de/abc_42.txt');

        $headLink = $this->createMock(AbstractStandalone::class);
        $headLink
            ->expects(self::once())
            ->method('__call')
            ->with('appendStylesheet', ['https://www.test.de/abc_42.txt', 'screen', '!IE', []]);

        $renderer = $this->createMock(PhpRenderer::class);
        $renderer
            ->expects(self::once())
            ->method('__call')
            ->with('headLink', [])
            ->willReturn($headLink);
        $renderer
            ->expects(self::never())
            ->method('plugin');

        $object = new RevisionHeadLink($minify, $renderer);

        $return = $object->appendStylesheet($href, 'screen', '!IE', ['rel' => 'prev']);

        self::assertSame($object, $return);
    }

    /**
     * @throws InvalidArgumentException
     * @throws BadMethodCallException
     */
    public function testAppendStylesheet2(): void
    {
        $href = 'https://www.test.de/abc.txt';

        $minify = $this->createMock(MinifyInterface::class);
        $minify
            ->expects(self::never())
            ->method('getPackageFiles');
        $minify
            ->expects(self::never())
            ->method('isItemOkToAddRevision');
        $minify
            ->expects(self::never())
            ->method('addRevision');

        $headLink = $this->createMock(AbstractStandalone::class);
        $headLink
            ->expects(self::once())
            ->method('__call')
            ->with('appendStylesheet', ['/test/abc.txt', 'screen', '!IE', []]);

        $renderer = $this->createMock(PhpRenderer::class);
        $renderer
            ->expects(self::once())
            ->method('__call')
            ->with('headLink', [])
            ->willReturn($headLink);
        $renderer
            ->expects(self::never())
            ->method('plugin');

        $object = new RevisionHeadLink($minify, $renderer);

        $return = $object->appendStylesheet(
            $href,
            'screen',
            '!IE',
            ['rel' => 'prev'],
            false,
            '/test',
            false,
        );

        self::assertSame($object, $return);
    }

    /**
     * @throws InvalidArgumentException
     * @throws BadMethodCallException
     */
    public function testPrependPackage(): void
    {
        $package = 'test-package';

        $minify = $this->createMock(MinifyInterface::class);
        $minify
            ->expects(self::once())
            ->method('getPackageFiles')
            ->with($package)
            ->willReturn(['files' => ['abc.txt', '', 'bcd.txt']]);
        $minify
            ->expects(self::once())
            ->method('isItemOkToAddRevision')
            ->with('css', '/abc.txt')
            ->willReturn(true);
        $minify
            ->expects(self::once())
            ->method('addRevision')
            ->with('/abc.txt')
            ->willReturn('/abc_42.txt');

        $headLink = $this->createMock(AbstractStandalone::class);
        $headLink
            ->expects(self::once())
            ->method('__call')
            ->with('prependStylesheet', ['https://www.test.de/abc_42.txt', 'screen', false, []]);

        $renderer = $this->createMock(PhpRenderer::class);
        $matcher  = self::exactly(4);
        $renderer
            ->expects($matcher)
            ->method('__call')
            ->willReturnCallback(
                static function (string $method, array $argv) use ($matcher, $headLink): string | AbstractStandalone {
                    match ($matcher->numberOfInvocations()) {
                        2 => self::assertSame('headLink', $method),
                        3 => self::assertSame('serverUrl', $method),
                        default => self::assertSame('baseUrl', $method),
                    };

                    match ($matcher->numberOfInvocations()) {
                        1 => self::assertSame(['bcd.txt', false, false], $argv),
                        2 => self::assertSame([], $argv),
                        3 => self::assertSame(['/abc_42.txt'], $argv),
                        default => self::assertSame(['abc.txt', false, false], $argv),
                    };

                    return match ($matcher->numberOfInvocations()) {
                        1 => '/abc.txt',
                        2 => $headLink,
                        3 => 'https://www.test.de/abc_42.txt',
                        default => '',
                    };
                },
            );
        $renderer
            ->expects(self::never())
            ->method('plugin');

        $object = new RevisionHeadLink($minify, $renderer);

        $return = $object->prependPackage($package);

        self::assertSame($object, $return);
    }

    /**
     * @throws InvalidArgumentException
     * @throws BadMethodCallException
     */
    public function testPrependPackage2(): void
    {
        $package = 'test-package';

        $minify = $this->createMock(MinifyInterface::class);
        $minify
            ->expects(self::once())
            ->method('getPackageFiles')
            ->with($package)
            ->willReturn([]);
        $minify
            ->expects(self::never())
            ->method('isItemOkToAddRevision');
        $minify
            ->expects(self::never())
            ->method('addRevision');

        $renderer = $this->createMock(PhpRenderer::class);
        $renderer
            ->expects(self::never())
            ->method('__call');
        $renderer
            ->expects(self::never())
            ->method('plugin');

        $object = new RevisionHeadLink($minify, $renderer);

        $return = $object->prependPackage($package);

        self::assertSame($object, $return);
    }

    /**
     * @throws InvalidArgumentException
     * @throws BadMethodCallException
     */
    public function testPrependPackage3(): void
    {
        $package = 'test-package';

        $minify = $this->createMock(MinifyInterface::class);
        $minify
            ->expects(self::once())
            ->method('getPackageFiles')
            ->with($package)
            ->willReturn(['files' => false]);
        $minify
            ->expects(self::never())
            ->method('isItemOkToAddRevision');
        $minify
            ->expects(self::never())
            ->method('addRevision');

        $renderer = $this->createMock(PhpRenderer::class);
        $renderer
            ->expects(self::never())
            ->method('__call');
        $renderer
            ->expects(self::never())
            ->method('plugin');

        $object = new RevisionHeadLink($minify, $renderer);

        $return = $object->prependPackage($package);

        self::assertSame($object, $return);
    }

    /**
     * @throws InvalidArgumentException
     * @throws BadMethodCallException
     */
    public function testPrependPackage4(): void
    {
        $package = 'test-package';

        $minify = $this->createMock(MinifyInterface::class);
        $minify
            ->expects(self::once())
            ->method('getPackageFiles')
            ->with($package)
            ->willReturn(['files' => []]);
        $minify
            ->expects(self::never())
            ->method('isItemOkToAddRevision');
        $minify
            ->expects(self::never())
            ->method('addRevision');

        $renderer = $this->createMock(PhpRenderer::class);
        $renderer
            ->expects(self::never())
            ->method('__call');
        $renderer
            ->expects(self::never())
            ->method('plugin');

        $object = new RevisionHeadLink($minify, $renderer);

        $return = $object->prependPackage($package);

        self::assertSame($object, $return);
    }

    /**
     * @throws InvalidArgumentException
     * @throws BadMethodCallException
     */
    public function testPrependStylesheet(): void
    {
        $href = 'https://www.test.de/abc.txt';

        $minify = $this->createMock(MinifyInterface::class);
        $minify
            ->expects(self::never())
            ->method('getPackageFiles');
        $minify
            ->expects(self::once())
            ->method('isItemOkToAddRevision')
            ->with('css', $href)
            ->willReturn(true);
        $minify
            ->expects(self::once())
            ->method('addRevision')
            ->with($href)
            ->willReturn('https://www.test.de/abc_42.txt');

        $headLink = $this->createMock(AbstractStandalone::class);
        $headLink
            ->expects(self::once())
            ->method('__call')
            ->with('prependStylesheet', ['https://www.test.de/abc_42.txt', 'screen', false, []]);

        $renderer = $this->createMock(PhpRenderer::class);
        $renderer
            ->expects(self::once())
            ->method('__call')
            ->with('headLink', [])
            ->willReturn($headLink);
        $renderer
            ->expects(self::never())
            ->method('plugin');

        $object = new RevisionHeadLink($minify, $renderer);

        $return = $object->prependStylesheet($href);

        self::assertSame($object, $return);
    }

    /**
     * @throws InvalidArgumentException
     * @throws BadMethodCallException
     */
    public function testPrependStylesheet2(): void
    {
        $href = 'https://www.test.de/abc.txt';

        $minify = $this->createMock(MinifyInterface::class);
        $minify
            ->expects(self::never())
            ->method('getPackageFiles');
        $minify
            ->expects(self::never())
            ->method('isItemOkToAddRevision');
        $minify
            ->expects(self::never())
            ->method('addRevision');

        $headLink = $this->createMock(AbstractStandalone::class);
        $headLink
            ->expects(self::once())
            ->method('__call')
            ->with('prependStylesheet', ['/test/abc.txt', 'screen', '!IE', []]);

        $renderer = $this->createMock(PhpRenderer::class);
        $renderer
            ->expects(self::once())
            ->method('__call')
            ->with('headLink', [])
            ->willReturn($headLink);
        $renderer
            ->expects(self::never())
            ->method('plugin');

        $object = new RevisionHeadLink($minify, $renderer);

        $return = $object->prependStylesheet(
            $href,
            'screen',
            '!IE',
            ['rel' => 'prev'],
            false,
            '/test',
            false,
        );

        self::assertSame($object, $return);
    }

    /** @throws InvalidArgumentException */
    public function testListPackage(): void
    {
        $package = 'test-package';

        $minify = $this->createMock(MinifyInterface::class);
        $minify
            ->expects(self::once())
            ->method('getPackageFiles')
            ->with($package)
            ->willReturn(['files' => ['abc.txt', '', 'bcd.txt']]);
        $minify
            ->expects(self::once())
            ->method('isItemOkToAddRevision')
            ->with('css', '/abc.txt')
            ->willReturn(true);
        $minify
            ->expects(self::once())
            ->method('addRevision')
            ->with('/abc.txt')
            ->willReturn('/abc_42.txt');

        $renderer = $this->createMock(PhpRenderer::class);
        $matcher  = self::exactly(3);
        $renderer
            ->expects($matcher)
            ->method('__call')
            ->willReturnCallback(
                static function (string $method, array $argv) use ($matcher): string {
                    match ($matcher->numberOfInvocations()) {
                        2 => self::assertSame('serverUrl', $method),
                        default => self::assertSame('baseUrl', $method),
                    };

                    match ($matcher->numberOfInvocations()) {
                        1 => self::assertSame(['abc.txt', false, false], $argv),
                        2 => self::assertSame(['/abc_42.txt'], $argv),
                        default => self::assertSame(['bcd.txt', false, false], $argv),
                    };

                    return match ($matcher->numberOfInvocations()) {
                        1 => '/abc.txt',
                        2 => 'https://www.test.de/abc_42.txt',
                        default => '',
                    };
                },
            );
        $renderer
            ->expects(self::never())
            ->method('plugin');

        $object = new RevisionHeadLink($minify, $renderer);

        $return = $object->listPackage($package);

        self::assertSame(
            [['path' => 'https://www.test.de/abc_42.txt', 'media' => 'screen', 'conditional' => false, 'extra' => []]],
            $return,
        );
    }

    /** @throws InvalidArgumentException */
    public function testListPackage2(): void
    {
        $package = 'test-package';

        $minify = $this->createMock(MinifyInterface::class);
        $minify
            ->expects(self::once())
            ->method('getPackageFiles')
            ->with($package)
            ->willReturn([]);
        $minify
            ->expects(self::never())
            ->method('isItemOkToAddRevision');
        $minify
            ->expects(self::never())
            ->method('addRevision');

        $renderer = $this->createMock(PhpRenderer::class);
        $renderer
            ->expects(self::never())
            ->method('__call');
        $renderer
            ->expects(self::never())
            ->method('plugin');

        $object = new RevisionHeadLink($minify, $renderer);

        $return = $object->listPackage($package);

        self::assertSame([], $return);
    }

    /** @throws InvalidArgumentException */
    public function testListPackage3(): void
    {
        $package = 'test-package';

        $minify = $this->createMock(MinifyInterface::class);
        $minify
            ->expects(self::once())
            ->method('getPackageFiles')
            ->with($package)
            ->willReturn(['files' => false]);
        $minify
            ->expects(self::never())
            ->method('isItemOkToAddRevision');
        $minify
            ->expects(self::never())
            ->method('addRevision');

        $renderer = $this->createMock(PhpRenderer::class);
        $renderer
            ->expects(self::never())
            ->method('__call');
        $renderer
            ->expects(self::never())
            ->method('plugin');

        $object = new RevisionHeadLink($minify, $renderer);

        $return = $object->listPackage($package);

        self::assertSame([], $return);
    }

    /** @throws InvalidArgumentException */
    public function testListPackage4(): void
    {
        $package = 'test-package';

        $minify = $this->createMock(MinifyInterface::class);
        $minify
            ->expects(self::once())
            ->method('getPackageFiles')
            ->with($package)
            ->willReturn(['files' => []]);
        $minify
            ->expects(self::never())
            ->method('isItemOkToAddRevision');
        $minify
            ->expects(self::never())
            ->method('addRevision');

        $renderer = $this->createMock(PhpRenderer::class);
        $renderer
            ->expects(self::never())
            ->method('__call');
        $renderer
            ->expects(self::never())
            ->method('plugin');

        $object = new RevisionHeadLink($minify, $renderer);

        $return = $object->listPackage($package);

        self::assertSame([], $return);
    }

    /** @throws InvalidArgumentException */
    public function testListPackage5(): void
    {
        $package = 'test-package';

        $minify = $this->createMock(MinifyInterface::class);
        $minify
            ->expects(self::once())
            ->method('getPackageFiles')
            ->with($package)
            ->willReturn(['files' => ['abc.txt', '', 'bcd.txt']]);
        $minify
            ->expects(self::once())
            ->method('isItemOkToAddRevision')
            ->with('css', '/abc.txt')
            ->willReturn(true);
        $minify
            ->expects(self::once())
            ->method('addRevision')
            ->with('/abc.txt')
            ->willReturn('/abc_42.txt');

        $renderer = $this->createMock(PhpRenderer::class);
        $matcher  = self::exactly(3);
        $renderer
            ->expects($matcher)
            ->method('__call')
            ->willReturnCallback(
                static function (string $method, array $argv) use ($matcher): string {
                    match ($matcher->numberOfInvocations()) {
                        2 => self::assertSame('serverUrl', $method),
                        default => self::assertSame('baseUrl', $method),
                    };

                    match ($matcher->numberOfInvocations()) {
                        1 => self::assertSame(['abc.txt', false, false], $argv),
                        2 => self::assertSame(['/abc_42.txt'], $argv),
                        default => self::assertSame(['bcd.txt', false, false], $argv),
                    };

                    return match ($matcher->numberOfInvocations()) {
                        1 => '/abc.txt',
                        2 => 'https://www.test.de/abc_42.txt',
                        default => '',
                    };
                },
            );
        $renderer
            ->expects(self::never())
            ->method('plugin');

        $object = new RevisionHeadLink($minify, $renderer);

        $return = $object->listPackage($package, 'print');

        self::assertSame(
            [['path' => 'https://www.test.de/abc_42.txt', 'media' => 'print', 'conditional' => false, 'extra' => []]],
            $return,
        );
    }

    /** @throws InvalidArgumentException */
    public function testListPackage6(): void
    {
        $package = 'test-package';

        $minify = $this->createMock(MinifyInterface::class);
        $minify
            ->expects(self::once())
            ->method('getPackageFiles')
            ->with($package)
            ->willReturn(['files' => ['abc.txt', '', 'bcd.txt']]);
        $minify
            ->expects(self::once())
            ->method('isItemOkToAddRevision')
            ->with('css', '/abc.txt')
            ->willReturn(true);
        $minify
            ->expects(self::once())
            ->method('addRevision')
            ->with('/abc.txt')
            ->willReturn('/abc_42.txt');

        $renderer = $this->createMock(PhpRenderer::class);
        $matcher  = self::exactly(2);
        $renderer
            ->expects($matcher)
            ->method('__call')
            ->willReturnCallback(
                static function (string $method, array $argv) use ($matcher): string {
                    self::assertSame('baseUrl', $method);

                    match ($matcher->numberOfInvocations()) {
                        1 => self::assertSame(['abc.txt', false, false], $argv),
                        default => self::assertSame(['bcd.txt', false, false], $argv),
                    };

                    return match ($matcher->numberOfInvocations()) {
                        1 => '/abc.txt',
                        default => '',
                    };
                },
            );
        $renderer
            ->expects(self::never())
            ->method('plugin');

        $object = new RevisionHeadLink($minify, $renderer);

        $return = $object->listPackage($package, 'print', false, [], false);

        self::assertSame(
            [['path' => '/abc_42.txt', 'media' => 'print', 'conditional' => false, 'extra' => []]],
            $return,
        );
    }
}
